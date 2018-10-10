<?php
/**
 * LinHUniX Web Application Framework
 *
 * @author Andrea Morello <andrea.morello@linhunix.com>
 * @copyright LinHUniX L.t.d., 2018, UK
 * @license   Proprietary See LICENSE.md
 * @version GIT:2018-v2
 */

namespace LinHUniX\Pdo\Query;

use LinHUniX\Mcp\masterControlProgram;
use LinHUniX\Pdo\Model\mcpQueryModelClass;

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
     * @author Andrea Morello <andrea.morello@linhunix.com>
     * @version GIT:2018-v1
     * @param Container $cfg Dipendecy injection for Pimple\Container
     * @param array $this->argIn temproraney array auto cleanable 
     * @return boolean|array query results 
     * @see [vendor]/mcp/Head.php caller of the config
 */
class jsonQuery  extends mcpQueryModelClass {
    private $querypath;
    public function __construct (masterControlProgram &$mcp, array $scopeCtl, array $scopeIn)
    {
        parent::__construct($mcp, $scopeCtl, $scopeIn);
        $this->querypath=$mcp->getResource("path.query");
        if (! is_dir($this->querypath)){
            $this->querypath=$mcp->getResource("path.module")."/query/";
            if (! is_dir($this->querypath)){
                $this->querypath=$mcp->getResource("path")."/cfg/query/";
            }
        }
        if (isset($scopeIn["J"])){
            if (file_exists($this->querypath.$scopeIn["J"])){
                $this->query=json_decode (file_get_contents ($this->querypath.$scopeIn["J"]));
            }else if (file_exists($this->querypath.$scopeIn["J"].".json")){
                $this->query=json_decode (file_get_contents ($this->querypath.$scopeIn["J"].".json"));
            }
        }
    }
}