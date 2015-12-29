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

namespace Sugarcrm\Sugarcrm\Console;

use Sugarcrm\Sugarcrm\Console\Quickstart\VagrantQuickstart;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sugarcrm\Sugarcrm\Logger\LoggerTransition;
use Sugarcrm\Sugarcrm\Console\Legacy\CronCommand;
use Sugarcrm\Sugarcrm\Console\Api\SearchStatusCommand;
use Sugarcrm\Sugarcrm\Console\Api\SearchReindexCommand;
use Sugarcrm\Sugarcrm\Console\Api\SearchFieldsCommand;
use Sugarcrm\Sugarcrm\Console\Api\ElasticsearchRoutingCommand;
use Sugarcrm\Sugarcrm\Console\Api\ElasticsearchQueueCommand;
use Sugarcrm\Sugarcrm\Console\Api\ElasticsearchIndicesCommand;

/**
 *
 * Console application invoked using `bin/sugarcrm`
 *
 * TODO: Add monolog-bridge for verbose console logging
 * TODO: Add parseable Table output
 * TODO: Add arg parsing at app level to avoid instantiating all cmds
 */
class SugarCliApplication extends Application
{
    /**
     * @var LoggerTransition
     */
    protected $logger;

    /**
     * @var \SugarConfig
     */
    protected $config;

    /**
     * @var boolean Is instance Active?
     */
    protected $activeInstance;

    /**
     * Ctor
     * @param string $version
     */
    public function __construct($version, LoggerTransition $logger = null)
    {

        if($this->activeInstance = $version != NULL){
            $this->logger = new LoggerTransition(\LoggerManager::getLogger());
            $this->config = \SugarConfig::getInstance();
        }
        parent::__construct('sugar-cli', $version);
    }

    /**
     * @return bool TRUE if command running within an active Sugar instance
     */
    public function isActiveInstance(){
        return $this->activeInstance;
    }

    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if($this->isActiveInstance()) {
            $this->setupAdminUser();
            $this->registerActiveInstanceCommands();
        } else {
            $this->registerQuickstartCommands();
        }
        return parent::doRun($input, $output);
    }

    /**
     * Register Quickstart commands
     */
    protected function registerQuickstartCommands(){
        $this->add(new VagrantQuickstart());
    }

    /**
     * Register active instance commands
     */
    protected function registerActiveInstanceCommands()
    {
        // API wrapper commands
        $this->add(new SearchReindexCommand());
        $this->add(new SearchStatusCommand());
        $this->add(new SearchFieldsCommand());
        $this->add(new ElasticsearchQueueCommand());
        $this->add(new ElasticsearchRoutingCommand());
        $this->add(new ElasticsearchIndicesCommand());

        // Legacy commands
        $this->add(new CronCommand($this->logger, $this->config, \SugarMetric_Manager::getInstance()));
    }

    /**
     * Setup current user as system admin
     */
    protected function setupAdminUser()
    {
        $GLOBALS['current_user'] = \BeanFactory::getBean('Users')->getSystemUser();
    }
}
