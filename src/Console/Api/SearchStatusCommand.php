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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

//if (file_exists('modules/Administration/clients/base/api/AdministrationApi.php')) {
//    require_once 'modules/Administration/clients/base/api/AdministrationApi.php';
//}

/**
 *
 * SearchEngine status
 *
 */
class SearchStatusCommand extends Command
{
    use ApiEndpointTrait;
    use LocalInstallTrait;

    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('search:status')
            ->setDescription('Show search engine availability and enabled modules')
        ;
    }

    public function getMinSugarVersion(){
        return "7.7.0";
    }

    /**
     * {inheritdoc}
     *
     * Return codes:
     * 0 = search available
     * 1 = search unavailable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setApi(new \AdministrationApi());

        $result = $this->callApi('searchStatus', array());
        $available = $result['available'];

        $table = new Table($output);
        $table->setHeaders(array('Enabled modules'));

        foreach ($result['enabled_modules'] as $module) {
            $table->addRow(array($module));
        }

        $table->render();

        if ($available) {
            $output->writeln("SearchEngine AVAILABLE");
        } else {
            $output->writeln("SearchEngine *NOT* available");
        }

        return $available ? 0 : 1;
    }
}
