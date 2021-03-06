<?php
/**
 * LinHUniX Web Application Framework
 *
 * @author Andrea Morello <lnxmcp@linhunix.com>
 * @copyright LinHUniX L.t.d., 2018, UK
 * @license   Proprietary See LICENSE.md
 * @version GIT:2018-v2
 */

namespace LinHUniX\Pdo\Model;

use LinHUniX\Mcp\Model\mcpBaseModelClass;
use LinHUniX\Mcp\masterControlProgram;

/**
 * @see [vendor]/mcp/Head.php caller of the config
 */
class mcpQueryModelClass extends mcpBaseModelClass
{
    const reqQueryNormal = "q";
    const reqFirstRowOnly = "f";
    const reqConuterOnly = "c";
    const reqExecuteOnly = "e";
    const reqExecuteAndClearCache = "ec";
    /**
     *
     * @var array parameters of the query to be generate 
     *  
     * $this->query is array
     * var ["T"]:
     *  e  = execute : exec query with boolean results  
     *  ec  = execute : exec query with boolean results  
     *  f  = firstRow : return only first row 
     *  q  = retrive array of all results 
     *  c  = return the count of the results 
     *  s  = return the sql information 
     *  r  = return sql and env 
     *  d  = search on cfg the data form another query
     *  p  = puth data on the database (using setrow) "Q" is table
     *  g  = get data on the database (using getrows) 
     *       "Q" is table "S" is sort , "L" limit and "W" where cause 
     *  other case return false;
     * var ["Q"] = query 
     * var ["E"] = env or instance of specific database 
     * var ["V"] = contain the values that need to remplace on query scripts 
     * var ["S"] = stored in Session
     * var ["G"] = stored in Glboal cfg
     * @author Andrea Morello <lnxmcp@linhunix.com>
     * @version GIT:2018-v1
     * @param Container $cfg Dipendecy injection for Pimple\Container
     * @param array $this->argIn temproraney array auto cleanable 
     * @return boolean|array query results 
     */
    protected $query;
    /**
     *
     * @param array (reference of) $scopeCtl => calling Controlling definitions  
     * @param array (reference of) $scopeIn temproraney array auto cleanable 
     * @param masterControlProgram to call back the father
     */
    public function __construct (masterControlProgram &$mcp, array $scopeCtl, array $scopeIn)
    {
        parent::__construct($mcp, $scopeCtl, $scopeIn);
        $this->query = array();
        $this->require = array("App.Service.pdo");
    }
    /**
     * 
     * @param string $string
     */
    public function setQuery($string)
    {
        $this->query["Q"] = $string;
    }
    /**
     * 
     * @param String $databaselabel the label on cfg of the pdoservice with out the "app."
     */
    public function setEnv($databaselabel)
    {
        $this->query["E"] = $databaselabel;
    }
    /**
     * 
     * @param String $flagType the flag for this specific request
     *  e  = execute : exec query with boolean results  
     *  er  = execute : exec query with rollback  
     *  f  = firstRow : return only first row 
     *  q  = retrive array of all results 
     *  c  = return the count of the results 
     *  s  = return the sql information 
     *  r  = return sql and env 
     *  d  = search on cfg the data form another query
     *  p  = puth data on the database (using setrow) "Q" is table
     *  g  = get data on the database (using getrows) 
     *       "Q" is table "S" is sort , "L" limit and "W" where cause 
     *  other case return false;
     */
    public function setRequestType($flagType)
    {
        $this->query["T"] = $flagType;
    }
    /**
     * 
     * @param array $varreq is array of request var 
     */
    public function setVar(array $varreq)
    {
        $this->query["V"] = $varreq;
    }
    /**
     * 
     * @param string $labeltostore the label to store this function on session
     */
    public function storeToSession($labeltostore)
    {
        $this->query["S"] = $labeltostore;
    }
    /**
     * 
     * @param string $labeltostore the label to store this function on Global cfg
     */
    public function storeGlobals($labeltostore)
    {
        $this->query["G"] = $labeltostore;
    }
    /**
     * 
     * @param string $labeltostore the label to store this function on zendcache
     */
    public function storeZendCache($labeltostore)
    {
        $this->query["Z"] = $labeltostore;
    }
    /**
     * return the Pdo Database Connector Class 
     * @return Pdo Class 
     */
    public function getPdo()
    {
        return $this->getMcp()->getResource("Service.pdo");
    }
    /**
     * execute the query, verify the results and store and return 
     * @return array response of code = like scope out;
     */
    protected function moduleCore()
    {
        $this->query["V"] = $this->argIn;
        $res = $this->getPdo()->run($this->argCtl, $this->query);
        if (isset($res["return"]))
        {
            $this->argOut = $res["return"];
        }
    }
    /**
     * Model Base to caputer execute an elabotrations about this
     * @param array (reference of) $scopeCtl => calling Controlling definitions
     * @param array (reference of) $scopeIn temproraney array auto cleanable
     * @return array response of code = like scope out;
     */
    public function run(array $scopeCtl, array $scopeIn) {
        try{
        return parent::run($scopeCtl,$scopeIn);
        }catch(\Exception $e){
            $this->getMcp()->waring("QueryError:".$e->get_message());
        }
    }
}
