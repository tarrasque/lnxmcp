<?php
/**
 * Created by PhpStorm.
 * User: linhunix
 * Date: 9/4/2018
 * Time: 10:11 AM
 */
namespace LinHUniX\Mcp\Model;

use LinHUniX\Mcp\masterControlProgram;

class mcpControllerModelClass extends mcpBaseModelClass
{
    /**
     *   @var string Controller Name
     */
    public $ClassName="mcpControllerBaseClass";
    /**
     *   @var string Controller Module
     */
    public $ClassModule="Mcp";
    /**
     *   @var string Controller Vendor
     */
    public $ClassVendor="LinHUniX";
    /**
     *   @var Service Module Vendor
     */
    public $Service=null;
    /**
     * getDriver extent the capacity of the  base class to have a facility to get a driver class
     *
     * @param  mixed $name
     *
     * @return service class
     */
    public function getService($name)
    {
        return $this->getMcp()->getResource("Service." . $name);
    }


    /**
     * @param array (reference of) $scopeCtl => calling Controlling definitions
     * @param array (reference of) $scopeIn temproraney array auto cleanable
     */
    public function __construct (masterControlProgram &$mcp, array $scopeCtl, array $scopeIn)
    {
        parent::__construct($mcp, $scopeCtl, $scopeIn);
        $this->ClassVendor=$scopeCtl["vendor"];
        $this->ClassModule=$scopeCtl["module"];
        $this->ClassName=$scopeCtl["name"];
        if ( !empty($this->ClassVendor) &&
             !empty($this->ClassName) &&
             !empty($this->ClassModule) )
        {
            $this->Service=$mcp->service($this->ClassModule,true,$mcp->getCommon(),$this->ClassModule,null,$this->ClassVendor);
            if ($this->Service==null){
                $this->Service=$mcp->service("main",true,$mcp->getCommon(),$this->ClassModule,null,$this->ClassVendor);
            }
            if ($this->Service instanceof mcpServiceModelClass) {
                $this->Service->runEvent("Init",$this->ClassName,$scopeIn);
            }
        }else {
            $modulemain=__DIR__."/../module.class.php";
            if (file_exists($modulemain)) {
                include_once($modulemain);
            }
        }
    }

}