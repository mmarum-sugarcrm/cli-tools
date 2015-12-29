<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

namespace Sugarcrm\Sugarcrm\Console\Legacy;

use Sugarcrm\Sugarcrm\Console\ActiveInstanceTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sugarcrm\Sugarcrm\Logger\LoggerTransition;

/**
 *
 * Reimplementation of cron.php into the Console framework.
 *
 */
class CronCommand extends Command
{
    use ActiveInstanceTrait;

    /**
     * @var LoggerTransition
     */
    protected $logger;

    /**
     * @var \SugarConfig
     */
    protected $config;

    /**
     * @var \SugarMetric_Manager
     */
    protected $metrics;

    public function __construct(LoggerTransition $logger, \SugarConfig $config, \SugarMetric_Manager $metrics)
    {
        parent::__construct();

        $this->logger = $logger;
        $this->config = $config;
        $this->metrics = $metrics;
    }

    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cron:execute')
            ->setDescription('Execute schedulers and queued jobs')
        ;
    }

    protected function getMinSugarVersion(){
        return "7.0.0";
    }

    /**
     * {inheritdoc}
     *
     * Return codes:
     * 0 = no execution errors
     * 1 = execution errors occured
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->metrics->setMetricClass('background')->setTransactionName('cron');
        $this->initGlobals();

        $this->logger->debug('--------------------------------------------> at cron.php <--------------------------------------------');

        $driver = $this->getCronDriver();
        $driver->runCycle();

        $this->cleanup();
        return $driver->runOk() ? 0 : 1;
    }

    /**
     * Initialize globals
     */
    protected function initGlobals()
    {
        global $current_language, $app_list_strings, $app_strings, $sugar_config;

        if (empty($current_language)) {
            $current_language = $sugar_config['default_language'];
        }

        $app_list_strings = return_app_list_strings_language($current_language);
        $app_strings = return_application_language($current_language);
    }

    /**
     * Get cron driver
     * @return SugarCronJobs
     */
    protected function getCronDriver()
    {
        $cronDriver = $this->config->get('cron_class', 'SugarCronJobs');
        $this->logger->debug("Using $cronDriver as CRON driver");
        \SugarAutoLoader::requireWithCustom("include/SugarQueue/$cronDriver.php");
        return new $cronDriver();
    }

    /**
     * Cleanup after execution.
     */
    protected function cleanup()
    {
        sugar_cleanup(false);

        // some jobs have annoying habit of calling sugar_cleanup(), and it can be called only once
        // but job results can be written to DB after job is finished, so we have to disconnect here again
        // just in case we couldn't call cleanup
        if (class_exists('DBManagerFactory')) {
            $db = \DBManagerFactory::getInstance();
            $db->disconnect();
        }

        // If we have a session left over, destroy it to avoid any session
        // buildup in the session storage backend.
        if (session_id()) {
            session_destroy();
        }
    }
}
