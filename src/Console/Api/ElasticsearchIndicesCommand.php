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

//require_once 'modules/Administration/clients/base/api/AdministrationApi.php';

/**
 *
 * Elasticsearch index status
 *
 */
class ElasticsearchIndicesCommand extends Command
{
    use ApiEndpointTrait;
    use ActiveInstanceTrait;

    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elasticsearch:indices')
            ->setDescription('Show Elasticsearch index statistics')
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

        $result = $this->callApi('elasticSearchIndices', array());

        $table = new Table($output);
        $table->setHeaders(array('Index', 'Docs', 'Size', 'Shards'));

        if ($result) {
            foreach ($result as $index => $status) {
                $docs = $status['indices'][$index]['docs']['num_docs'];
                $size = $status['indices'][$index]['index']['size_in_bytes'];
                $shards = $status['_shards']['total'];
                $table->addRow([$index, $docs, $size, $shards]);
            }
        }

        $table->render();
    }
}
