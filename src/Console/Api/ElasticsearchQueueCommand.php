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

use Sugarcrm\Sugarcrm\Console\ActiveInstanceTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

//require_once 'modules/Administration/clients/base/api/AdministrationApi.php';

/**
 *
 * Elasticsearch queue status
 *
 */
class ElasticsearchQueueCommand extends Command
{
    use ApiEndpointTrait;
    use ActiveInstanceTrait;

    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elasticsearch:queue')
            ->setDescription('Show Elasticsearch queue statistics')
        ;
    }

    public function getMinSugarVersion(){
        return "7.7.0";
    }

    /**
     * {inheritdoc}
     *
     * Return codes:
     * 0 = queue is empty
     * 1 = queue is not empty
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setApi(new \AdministrationApi());

        $result = $this->callApi('elasticSearchQueue', array());

        $table = new Table($output);
        $table->setHeaders(array('Module', 'Count'));

        if ($result['queued']) {
            foreach ($result['queued'] as $module => $count) {
                $table->addRow([$module, $count]);
            }
            $table->addRow(new TableSeparator());
        }

        $table->addRow(array('Total', $result['total']));
        $table->render();
    }
}
