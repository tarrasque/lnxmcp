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
use LinHUniX\Mcp\Model\mcpDebugModelClass;
use LinHUniX\Mcp\Model\mcpServiceProviderModelClass;

class loggerProviderModel implements mcpServiceProviderModelClass
{
    /**
     * Register the settings as a provider with a container
     *
     */
    public function register (masterControlProgram &$mcp, mcpConfigArrayModelClass &$cfg)
    {
        $level = $mcp->getCfg ("app.level");
        if ($level == null) {
            $level = mcpDebugModelClass::DEBUG;
        }
        $mcp->setCfg ("app.level", $level);
        $mcp->setCfg ("Logger", new mcpDebugModelClass($mcp, $level));
        return $cfg;
    }

}