<?php
/**
 * LinHUniX Web Application Framework
 *
 * @author Andrea Morello <andrea.morello@linhunix.com>
 * @copyright LinHUniX L.t.d., 2018, UK
 * @license   Proprietary See LICENSE.md
 * @version GIT:2018-v2
 */

namespace LinHUniX\Mcp\Component;

use LinHUniX\Mcp\masterControlProgram;

/**
 * Description of mcpDebugClass
 *
 * @author andrea
 */
class mcpMenuClass {
  /////////////////////////////////////////////////////////////////////////////
    // ARRAY CALLER MODULE
    /////////////////////////////////////////////////////////////////////////////

    /**
     * Run a command inside $scopeCtl
     *
     * @param  mixed $scopectl
     * @param  mixed $scopeIn
     * @return any $ScopeOut
     */
    public static function runCommand(array $scopectl,array $scopeIn=array()){
        $callname="none";
        if(isset($scopectl["name"])){
            $callname=$scopectl["name"];
        }
        $path = null;
        if(isset($scopectl["path"])){
            $path=$scopectl["path"];
        }
        $ispreload = false;
        if(isset($scopectl["ispreload"])){
            $ispreload=$scopectl["ispreload"];
        }
        $modinit=null;
        if(isset($scopectl["modinit"])){
            $modinit=$scopectl["modinit"];
        }
        if(isset($scopectl["module"])){
            $modinit=$scopectl["module"];
        }
        $vendor = null;
        if(isset($scopectl["vendor"])){
            $vendor=$scopectl["vendor"];
        }
        $subcall = null;
        if(isset($scopectl["subcall"])){
            $subcall=$scopectl["subcall"];
        }
        $type=null;
        if (isset($scopectl["type"])){
            $type=$scopectl["type"];
        }
        $controllerModule=null;
        if (isset($scopectl["controllerModule"])){
            $controllerModule=$scopectl["controllerModule"];
        }
        $blockModule=null;
        if (isset($scopectl["blockModule"])){
            $blockModule=$scopectl["blockModule"];
        }
        $result=null;
        lnxmcp()->info ("command try to call ".$type.">> app." . $callname);
        switch($type){
            case "exit":
                DumpAndExit(@$scopectl["message"]);
                break;
            case "print":
                echo $scopeIn;
                break;
            case "clear":
                $scopeIn=array();
                break;
            case "header":
                $header=@$scopectl["header"];
                lnxmcp()->header($header,false);
                break;
            case "headerClose":
                $header=@$scopectl["header"];
                lnxmcp()->header($header,true);
                break;
            case "run":
                $result=lnxmcp()->moduleRun($callname,$scopeIn);
                break;
            case "driver":
                $result=lnxmcp()->driver($callname,$ispreload,$scopeIn,$modinit,$subcall,$vendor);                 
                break;
            case "query":
                $result=lnxmcp()->queryR($callname,$ispreload,$scopeIn,$modinit,$subcall,$vendor);                 
                break;
            case "queryCommon":
                $result=lnxmcp()->queryCommonR($callname,$ispreload,$scopeIn,$modinit,$subcall);                 
                break;
            case "controller":
                $result=lnxmcp()->controller($callname,$ispreload,$scopeIn,$modinit,$subcall,$vendor);                 
                break;
            case "controllerCommon":
                $result=lnxmcp()->controllerCommon($callname,$ispreload,$scopeIn,$modinit,$subcall);                 
                break;
            case "api":
                $result=lnxmcp()->api($callname,$ispreload,$scopeIn,$modinit,$subcall,$vendor);                 
                break;
            case "apiCommon":
                $result=lnxmcp()->apiCommon($callname,$ispreload,$scopeIn,$modinit,$subcall);                 
                break;
            case "service":
                $result=lnxmcp()->service($callname,$ispreload,$scopeIn,$modinit,$subcall,$vendor);                 
                break;
            case "serviceCommon":
                $result=lnxmcp()->serviceCommon($callname,$ispreload,$scopeIn,$modinit,$subcall);                 
                break;
            case "page":
                $result=lnxmcp()->page($callname,$scopeIn,$modinit,$vendor);                 
                break;
            case "mail":
                $result=lnxmcp()->mail($callname,$scopeIn,$modinit);                 
                break;
            case "block":
                $result=lnxmcp()->block($callname,$scopeIn,$modinit,$vendor);                 
                break;
            case "blockCommon":
                $result=lnxmcp()->blockCommon($callname,$scopeIn,$modinit);               
                break;
            case "showPage":
                $result=lnxmcp()->showPage($callname,$scopeIn,$controllerModule,$blockModule);               
                break;
            case "showCommonPage":
                $result=lnxmcp()->showCommonPage($callname,$scopeIn,$controllerModule,$blockModule);               
                break;
            case "showBlock":
                $result=lnxmcp()->showBlock($callname,$scopeIn,$controllerModule,$blockModule);               
                break;
            case "showCommonBlock":
                $result=lnxmcp()->showCommonBlock($callname,$scopeIn,$controllerModule,$blockModule);               
                break;
            case "showFullCommonBlock":    
                $result=lnxmcp()->showFullCommonBlock($callname,$scopeIn,$controllerModule,$blockModule);               
                break;
            default:
                $result=lnxmcp()->module($callname,$path,$ispreload,$scopeIn,$modinit,$subcall,$vendor,$type);                 
        }
        return $result;
    }
        /**
     * runSequence inside actions
     * @param  mixed $actions
     * @param  mixed $scopeIn
     * @return any $ScopeOut
     */
    public static function runSequence (array $actionsSeq,$scopeIn=array())
    {  
        if ($actionsSeq==null){
            lnxmcp()->warning("sequence null!!");
            return $scopeIn;
        }
        foreach ($actionsSeq as $callname=>$scopeCtl){
            lnxmcp()->info ("Sequence call app." . $callname);
            if (!isset( $scopeCtl["name"])){
                $scopeCtl["name"]=$callname;
            }
            if (isset($scopeCtl["input"])){
                foreach($scopeCtl["input"] as $sik=>$siv){
                    $scopeIn[$sik]=$siv;
                }
            }
            $scopeIn=lnxmcp()->runCommand($scopeCtl,$scopeIn);          
        }
        return $scopeIn;
    }
        /**
     * Run Module as Menu sequence
     * @param string $action name of the Doctrine
     * @param array $scopeIn   Input Array with the value need to work
     * @return any $ScopeOut
     */
    public static function runMenu ($action,$scopeIn=array())
    {
        $sequence=lnxmcp()->getResource("menu.".$action);
        if ($sequence==null){
            $seqpth=lnxmcp()->getResource("path.menus");
            if ($seqpth!=null){
                $sequence=lnxGetJsonFile($action,$seqpth,"json");
            }
        }
        if (($sequence!=null)&&($sequence!=false)){
            return lnxmcp()->runSequence($sequence,$scopeIn);
        }else{
            return false;
        }
    }
     /**
     * Run Module as Tags sequence
     * @param string $action name of the Doctrine
     * @param array $scopeIn   Input Array with the value need to work
     * @return any $ScopeOut
     */
    public function runTag ($action,$scopeIn=array())
    {
        $sequence=lnxmcp()->getResource("tag.".$action);
        if ($sequence==null){
            $seqpth=lnxmcp()->getResource("path.tags");
            if ($seqpth!=null){
                $sequence=lnxGetJsonFile($action,$seqpth,"json");
            }
        }
        if (($sequence!=null)&&($sequence!=false)){
            return lnxmcp()->runSequence($sequence,$scopeIn);
        }else{
            return false;
        }
    }
}