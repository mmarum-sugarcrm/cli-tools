<?php
/**
 * Created by PhpStorm.
 * User: mmarum
 * Date: 12/29/15
 * Time: 5:54 PM
 */

namespace Sugarcrm\Sugarcrm\Console;

/**
 * Trait for Commands (@see Symfony\Component\Console\Command\Command) that need a local Sugar install to function correctly.
 *
 * Class LocalInstallTrait
 * @package Sugarcrm\Sugarcrm\Console
 */
trait LocalInstallTrait
{

    /**
     * @return string Full version string for minimum supported Sugar version for this command (for example, "7.0.0")
     */
    protected abstract function getMinSugarVersion();

    /**
     * Commands should only be enabled if they are supported by current Sugar instance.
     *
     * Default implementation performs a minimum Sugar version check. Override to implement additional requirements.
     * @see Symfony\Component\Console\Command|isEnabled()
     * @override
     */
    public function isEnabled(){
        global $sugar_version;
        return $sugar_version && (version_compare($sugar_version, $this->getMinSugarVersion()) >= 0);
    }
}
