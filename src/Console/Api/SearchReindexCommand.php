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

namespace Sugarcrm\Sugarcrm\Console\Api;

use Sugarcrm\Sugarcrm\Console\LocalInstallTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 *
 * SearchEngine schedule full reindex
 *
 */
class SearchReindexCommand extends Command
{
    use ApiEndpointTrait;
    use LocalInstallTrait;

    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('search:reindex')
            ->setDescription('Schedule SearchEngine reindex')
            ->addOption(
                'modules',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma separated list of modules to be reindexed. Defaults to all search enabled modules.'
            )
            ->addOption(
                'clearData',
                null,
                InputOption::VALUE_NONE,
                'Clear the data of the involved index/indices before reindexing the records.'
            )
        ;
    }

    public function getMinSugarVersion(){
        return "7.7.0";
    }

    /**
     * {inheritdoc}
     *
     * Return codes:
     * 0 = scheduling sucessfull
     * 1 = scheduling error
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setApi(new \AdministrationApi());

        $result = $this->callApi('searchReindex', array(
            'module_list' => $input->getOption('modules'),
            'clear_data' => $input->getOption('clearData'),
        ));

        $status = $result['success'];

        if ($status) {
            $output->writeln('Reindex succesfully scheduled');
        } else {
            $output->writeln('Something went wrong, check your logs');
        }

        return $status ? 0 : 1;
    }
}
