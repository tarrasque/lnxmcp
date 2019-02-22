<?php
/**
 * LinHUniX Web Application Framework
 *
 * @author Andrea Morello <andrea.morello@linhunix.com>
 * @copyright LinHUniX L.t.d., 2018, UK
 * @license   Proprietary See LICENSE.md
 * @version GIT:2019-v3
 */

namespace LinHUniX\Mcp\Component;

use LinHUniX\Mcp\masterControlProgram;

/**
 * Description of mcpTemplateClass
 *
 * @author andrea
 */
class mcpTemplateClass {

    /**
     * TEMPLATE (NEDD TO DO MORE)
     * @param string $callname name of the functionality
     * @param string $path     path where present the basedirectory of the data
     * @param bool $ispreload  is only a preload (ex page) or need to be execute (ex controller)
     * @param array $scopeIn   Input Array with the value need to work
     * @param string $modinit  Module name where is present the code and be load and initalized
     * @param string $subcall  used if the name of the functionality ($callname) and the subcall are different
     * @param string $vendor   this code is part of specific vendor (ex ft )
     * @param string $type     is a Page, Block, Controller, Driver
     * @param bool $hasreturn if is called the objet return the value as string 
     * @return String Output
     */
    public static function  template ($callname, $path = null, $ispreload = false, $scopeIn = array (), $modinit = null, $subcall = null, $vendor = null, $type = null,$hasreturn=false)
    {
         if ($hasreturn==true){
            ob_start();
        }
        lnxmcp()->statmentModule ($path, $callname, $ispreload, $scopeIn, $modinit, $subcall, $vendor, $type);
        lnxmcp()->loadModule ();
        if ($hasreturn==true){
            $lres=ob_get_contents();
            ob_end_clean();
            return $lres;
        }
        return null;
    }
}