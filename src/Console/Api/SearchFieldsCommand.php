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
use Symfony\Component\Console\Helper\Table;

//require_once 'modules/Administration/clients/base/api/AdministrationApi.php';

/**
 *
 * SearchEngine fields
 *
 */
class SearchFieldsCommand extends Command
{
    use ApiEndpointTrait;
    use LocalInstallTrait;

    /**
     * {inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('search:fields')
            ->setDescription('Show search engine enabled fields')
            ->addOption(
                'modules',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma separated list of modules.'
            )
            ->addOption(
                'searchOnly',
                null,
                InputOption::VALUE_NONE,
                'Show searchable fields only.'
            )
            ->addOption(
                'byBoost',
                null,
                InputOption::VALUE_NONE,
                'Order the output by boost value.'
            )
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

        $args = array();

        if ($modules = $input->getOption('modules')) {
            $args['module_list'] = $modules;
        }

        if ($searchOnly = $input->getOption('searchOnly')) {
            $args['search_only'] = true;
        }

        if ($byBoost = $input->getOption('byBoost')) {
            $args['order_by_boost'] = true;
        }


        $result = $this->callApi('searchFields', $args);

        // handle output which is different when ordered by boost
        $table = new Table($output);

        if ($byBoost) {

            $table->setHeaders(array('Module', 'Field', 'Boost'));

            foreach ($result as $raw => $boost) {
                $raw = explode('.', $raw);
                $table->addRow([$raw[0], $raw[1], $boost]);
            }

        } else {

            $table->setHeaders(array('Module', 'Field', 'Type', 'Searchable', 'Boost'));

            foreach ($result as $module => $fields) {
                foreach ($fields as $field => $props) {
                    $searchAble = !empty($props['searchable']) ? 'yes' : 'no';
                    $boost = isset($props['boost']) ? $props['boost'] : 'n/a';
                    $table->addRow([$module, $field, $props['type'], $searchAble, $boost]);
                }
            }
        }

        $table->render();
    }
}
