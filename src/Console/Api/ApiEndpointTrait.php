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

/**
 *
 * Wrapper for \SugarApi endpoint calls
 *
 */
trait ApiEndpointTrait
{
    /**
     * @var \SugarApi
     */
    protected $api;

    /**
     * @var \RestService
     */
    protected $service;

    /**
     * Set API class
     * @param \SugarApi $api
     */
    protected function setApi(\SugarApi $api)
    {
        $this->api = $api;
        $this->service = new \RestService();
    }

    /**
     *
     * @param unknown $method
     * @param array $args
     */
    protected function callApi($method, array $args = array())
    {
        $args = array($this->service, $args);
        return call_user_func_array(array($this->api, $method), $args);
    }
}
