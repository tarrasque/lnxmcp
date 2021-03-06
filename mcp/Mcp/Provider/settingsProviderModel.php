<?php
/**
 * LinHUniX Web Application Framework
 *
 * @author    Andrea Morello <lnxmcp@linhunix.com>
 * @copyright LinHUniX L.t.d., 2018, UK
 * @license   Proprietary See LICENSE.md
 * @version   GIT:2018-v2
 */

namespace LinHUniX\Mcp\Provider;

use LinHUniX\Mcp\masterControlProgram;
use LinHUniX\Mcp\Model\mcpConfigArrayModelClass;
use LinHUniX\Mcp\Model\mcpServiceProviderModelClass;

class settingsProviderModel implements mcpServiceProviderModelClass
{
    private $mcp;

    private function loadCfgArray ($cfgfile)
    {
        if (file_exists ($cfgfile)) {
            try {
                $cfgdata = json_decode (file_get_contents ($cfgfile),true);
                foreach ($cfgdata as $ck => $cv) {
                    $this->mcp->setCfg ($ck, $cv);
                }
            } catch (\Exception $e) {
                error_log ("settingsProvider:" . $cfgfile . "=" . $e->getMessage ());
            }
        }
    }

    private function loadCfgValue ($tag, $cfgfile, $defvalue = null)
    {
        if (file_exists ($cfgfile)) {
            try {
                $this->mcp->setCfg ($tag, file_get_contents ($cfgfile));
            } catch (\Exception $e) {
                error_log ("settingsProvider:" . $tag . "=" . $e->getMessage ());
            }
        } else {
            if ($defvalue != null) {
                $this->mcp->setCfg ($tag, $defvalue);
            }
        }
    }

    /**
     * Register the settings as a provider with a Pimple container
     *
     */
    public function register (masterControlProgram &$mcp, mcpConfigArrayModelClass &$cfg)
    {
        $this->mcp =& $mcp;
        $env = $cfg['app.env'];
        date_default_timezone_set ($cfg["app.timezone"]);
        //////////////////////////////////////////////////////
        ///  READ CONFIG DOMINE
        //////////////////////////////////////////////////////
        if (!isset($_SERVER["HTTP_HOST"])){
            $_SERVER["HTTP_HOST"]="default";
        }
        $cfgfile = $cfg['app.path.config'] . '/config.' . $_SERVER["HTTP_HOST"] . '.json';
        $this->loadCfgArray ($cfgfile);
        //////////////////////////////////////////////////////
        ///  READ CONFIG BY ENV
        //////////////////////////////////////////////////////
        $cfgfile =  $cfg['app.path.config'] . '/config.' . $env . '.json';
        $this->loadCfgArray ($cfgfile);
        $mcp->setCfg ("app.env", $env);
        //////////////////////////////////////////////////////
        ///  CHECK SETTINGS
        //////////////////////////////////////////////////////
        if (!isset($cfg["settings"])) {
            $mcp->setCfg ("settings", array ());
        }
        //////////////////////////////////////////////////////
        ///  READ VERSION
        //////////////////////////////////////////////////////
        $verfile = $cfg['app.path'] . '/VERSION';
        $this->loadCfgValue ("app.ver", $verfile, "0.0.1");
        //////////////////////////////////////////////////////
        ///  Read timezone
        //////////////////////////////////////////////////////
        date_default_timezone_set ($cfg["app.timezone"]);
        return $cfg;
    }
}
