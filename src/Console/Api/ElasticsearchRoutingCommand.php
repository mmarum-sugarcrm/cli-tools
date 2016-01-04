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

//require_once 'modules/Administration/clients/base/api/AdministrationApi.php';

/**
 *
 * Elasticsearch routing status
 *
 */
class ElasticsearchRoutingCommand extends Command
{
    use ApiEndpointTrait;
    use LocalInstallTrait;

    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elasticsearch:routing')
            ->setDescription('Show Elasticsearch index routing')
        ;
    }

    public function getMinSugarVersion(){
        return "7.7.0";
    }

    /**
     * {inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setApi(new \AdministrationApi());

        $result = $this->callApi('elasticSearchRouting', array());

        $table = new Table($output);
        $table->setHeaders(array('Module', 'Strategy', 'Write index', 'Read indices'));

        foreach ($result as $module => $entry) {
            $table->addRow([
                $module,
                $entry['strategy'],
                $entry['routing']['write_index'],
                implode(',', $entry['routing']['read_indices']),
            ]);
        }

        $table->render();
    }
}
