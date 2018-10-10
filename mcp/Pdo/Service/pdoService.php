<?php
/**
 * LinHUniX Web Application Framework
 *
 * @author Andrea Morello <andrea.morello@linhunix.com>
 * @copyright LinHUniX L.t.d., 2018, UK
 * @license   Proprietary See LICENSE.md
 * @version GIT:2018-v2
 */

namespace LinHUniX\Pdo\Service;

use LinHUniX\Mcp\Model\mcpBaseModelClass;
use LinHUniX\Mcp\masterControlProgram;

class pdoService extends mcpBaseModelClass
{
    const reqNormalQuery = "q";
    const reqFirstRowOnly = "f";
    const reqConuntAllResultOnly = "c";
    const reqExecuteOnly = "e";
    const reqExecuteAndResetCache = "ec";
    private $preloadData = array();
    private $preloadEnv = array();
    /**
     * @param array (reference of) $scopeCtl => calling Controlling definitions  
     * @param array (reference of) $scopeIn temproraney array auto cleanable 
     */
    public function __construct (masterControlProgram &$mcp, array $scopeCtl, array $scopeIn)
    {
        parent::__construct($mcp, $scopeCtl, $scopeIn);
        $this->getMcp()->info("load var");
        if (isset($scopeIn["ENV"]))
        {
            $this->preloadEnv = $scopeIn["ENV"];
        }
        if (isset($scopeIn["DATA"]))
        {
            $this->preloadData = $scopeIn["DATA"];
        }
        $this->getMcp()->info("preload var");
        $this->preload();
    }
    /**
     * $scope array is 
     * var ["T"]:
     *  e  = execute : exec query with boolean results  
     *  ec  = execute : exec query with boolean results and reset all cache  
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
     * var ["S"] = stored in session 
     * var ["G"] = stored in globals 
     * @author Andrea Morello <andrea.morello@linhunix.com>
     * @version GIT:2018-v1
     * @param Container $cfg Dipendecy injection for Pimple\Container
     * @param array $this->argIn temproraney array auto cleanable 
     * @return boolean|array query results 
     */
    public function moduleCore()
    {
        if ((empty($this->argIn["Q"]) || (empty($this->argIn["E"]))))
        {
            return false;
        }
        if ($this->GetFromSession())
        {
            return;
        }
        if ($this->getFromGlobal())
        {
            return;
        }
        $this->where();
        try
        {
            switch ($this->argIn["T"])
            {
                case "e":
                    $drv = $this->getDatabase($this->argIn["E"]);
                    if ($drv != null)
                    {
                        $this->argOut = $drv->execute($this->argIn["Q"], $this->argIn["V"]);
                    }
                    break;
                case "ec":
                    $drv = $this->getDatabase($this->argIn["E"]);
                    if ($drv != null)
                    {
                        $this->argOut = $drv->execute($this->argIn["Q"], $this->argIn["V"]);
                    }
                    $this->cleanAllCache();
                    break;
                case "f":
                    $drv = $this->getDatabase($this->argIn["E"]);
                    if ($drv != null)
                    {
                        $this->argOut = $drv->firstRow($this->argIn["Q"], $this->argIn["V"]);
                    }
                    break;
                case "q":
                    $drv = $this->getDatabase($this->argIn["E"]);
                    if ($drv != null)
                    {
                        $this->argOut = $drv->simpleQuery($this->argIn["Q"], $this->argIn["V"]);
                    }
                    break;
                case "c":
                    $drv = $this->getDatabase($this->argIn["E"]);
                    if ($drv != null)
                    {
                        $this->argOut = $drv->simpleCount($this->argIn["Q"], $this->argIn["V"]);
                    }
                    break;
                case "p":
                    $drv = $this->getDatabase($this->argIn["E"]);
                    if ($drv != null)
                    {
                        $this->argOut = $drv->setRow($this->argIn["V"], $this->argIn["Q"]);
                    }
                    break;
                case "g":
                    if (isset($this->argIn["S"]) && isset($this->argIn["L"]))
                    {
                        $drv = $this->getDatabase($this->argIn["E"]);
                        if ($drv != null)
                        {
                            $this->argOut = $drv->getRows($this->argIn["V"], $this->argIn["Q"], $this->argIn["S"], $this->argIn["L"], @$this->argIn["W"]);
                        }
                    }
                    break;
                case "s":
                    $this->argOut = $this->argIn["Q"];
                    break;
                case "r":
                    $this->argOut = array(
                        "sql" => $this->argIn["Q"],
                        "env" => $this->argIn["E"]
                    );
                    break;
                case "d":
                    $this->argOut = $this->getMcp()->getResource("db." . $this->argIn["Q"]);
                    break;
            }
            $this->putToSession();
            $this->putToGlobal();
        } catch (Exception $e)
        {
            $this->getMcp()->warning("QueryIdx:Index=" . $this->argIn["I"] . ",Error:" . $e->getMessage());
        }
    }
    /**
     * This a fast where cause creator 
     * $scope["WhereFullString"] has the string with where clause  (example) " pippo='pluto' "
     * $scope["WhereArray"] has the array as key and value (example)  " pippo='pluto' "
     * $scope["WhereGet"] has the array as key and value (example)  " pippo=$_GET['pluto'] "
     * $scope["WherePost"] has the array as key and value (example)  " pippo=$_POST['pluto'] "
     * $scope["WhereSession"] has the array as key and value (example)  " pippo=$SESSION['pluto'] "
     * $scope["WhereDic"] has the array as key and value (example)  " pippo=$cfg['pluto'] "
     */
    public function where()
    {
        $where = " 1 = 1 ";
        if (isset($this->argIn["V"]["WhereFullString"]))
        {
            $where .= " AND " . $this->argIn["V"]["WhereFullString"];
            unset($this->argIn["V"]["WhereFullString"]);
        }
        if (isset($this->argIn["V"]["WhereArray"]))
        {
            foreach ($this->argIn["V"]["WhereArray"] as $key => $value)
            {
                $where .= " AND " . $key . " = '" . $value . "' ";
            }
            unset($this->argIn["V"]["WhereArray"]);
        }
        if (isset($this->argIn["V"]["WhereGet"]))
        {
            foreach ($this->argIn["V"]["WhereGet"] as $key => $value)
            {
                $strin = "";
                if (isset($_GET[$value]))
                {
                    $strin = $_GET[$value];
                }
                $where .= " AND " . $key . " = '" . $strin . "' ";
            }
            unset($this->argIn["V"]["WhereGet"]);
        }
        if (isset($this->argIn["V"]["WherePost"]))
        {
            foreach ($this->argIn["V"]["WherePost"] as $key => $value)
            {
                $strin = "";
                if (isset($_POST[$value]))
                {
                    $strin = $_POST[$value];
                }
                $where .= " AND " . $key . " = '" . $strin . "' ";
            }
            unset($this->argIn["V"]["WherePost"]);
        }
        if (isset($this->argIn["V"]["WhereSession"]))
        {
            foreach ($this->argIn["V"]["WhereSession"] as $key => $value)
            {
                $strin = "";
                if (isset($_SESSION[$value]))
                {
                    $strin = $_SESSION[$value];
                }
                $where .= " AND " . $key . " = '" . $strin . "' ";
            }
            unset($this->argIn["V"]["WhereSession"]);
        }
        if (isset($this->argIn["V"]["WhereDic"]))
        {
            foreach ($this->argIn["V"]["WhereDic"] as $key => $value)
            {
                $strin = "";
                if (isset($GLOBALS["cfg"][$value]))
                {
                    $strin = $GLOBALS["cfg"][$value];
                }
                $where .= " AND " . $key . " = '" . $strin . "' ";
            }
            unset($this->argIn["V"]["WhereDic"]);
        }
        $this->argIn["V"]["WHERE"] = $where;
    }
    /**
     * 
     * @param \LinHUniX\Pdo\Service\Container $cfg
     * @param array $scope
     */
    private function preload()
    {
        foreach ($this->preloadEnv as $env => $subscope)
        {
            $this->getMcp()->info("LOAD DATABASE " . $env . ":" . $subscope["driver"]);
            $this->getMcp()->driver($env, true, $subscope, "Pdo", $subscope["driver"]);
        }
    }
    /**
     *  @param string $dbname database name 
     * @return Driver database pdo driver
     */
    private function getDatabase($dbname)
    {
        $mcp = $this->getMcp();
        $res = $mcp->getResource("Driver." . $dbname);
        return $res;
    }
    private function GetFromSession()
    {
        if (!isset($_SESSION))
        {
            return;
        }
        if (!isset($_SESSION["pdo.cache"]))
        {
            $_SESSION["pdo.cache"] = array();
        }
        if (isset($this->argIn["S"]))
        {
            if (!empty($this->argIn["S"]))
            {
                $lblS = $this->argIn["S"];
                $this->getMcp()->debug("try to read from session:" . $lblS);
                if (isset($_SESSION["pdo.cache"][$lblS]))
                {
                    if (!empty($_SESSION["pdo.cache"][$lblS]))
                    {
                        $this->argOut = $_SESSION["pdo.cache"][$lblS];
                        $this->getMcp()->debug("read from session:" . $lblS);
                        return true;
                    } else
                    {
                        $this->getMcp()->debug("can't read (empty) from session:" . $lblS);
                    }
                }
            }
        }
        return false;
    }
    private function getFromGlobal()
    {
        if (!isset($GLOBALS["pdo.cache"]))
        {
            $GLOBALS["pdo.cache"] = array();
        }
        if (isset($this->argIn["G"]))
        {
            if (!empty($this->argIn["G"]))
            {
                $lblG = $this->argIn["G"];
                $this->getMcp()->debug("try to read from global:" . $lblG);
                if (isset($GLOBALS["pdo.cache"][$lblG]))
                {
                    if (!empty($GLOBALS["pdo.cache"][$lblG]))
                    {
                        $this->argOut = $GLOBALS["pdo.cache"][$lblG];
                        $this->getMcp()->debug("read from global:" . $lblG);
                        return true;
                    } else
                    {
                        $this->getMcp()->debug("can't read (empty) from global:" . $lblG);
                    }
                }
            }
        }
        return false;
    }
    private function putToGlobal()
    {
        if (isset($this->argIn["G"]))
        {
            if (!isset($GLOBALS["pdo.cache"]))
            {
                $GLOBALS["pdo.cache"] = array();
            }
            if (!empty($this->argIn["G"]))
            {
                $lblG = $this->argIn["G"];
                $GLOBALS["pdo.cache"][$lblG] = $this->argOut;
                $this->getMcp()->debug("store from global:" . $lblG);
            }
        }
    }
    private function putToSession()
    {

        if (isset($this->argIn["S"]))
        {
            if (!isset($_SESSION))
            {
                return;
            }
            if (!isset($_SESSION["pdo.cache"]))
            {
                $_SESSION["pdo.cache"] = array();
            }
            if (!empty($this->argIn["S"]))
            {
                $lblS = $this->argIn["S"];
                $_SESSION["pdo.cache"][$lblS] = $this->argOut;
                $this->getMcp()->debug("store from session:" . $lblS);
            }
        }
    }

    private function cleanAllCache()
    {
        if (isset($GLOBALS["cfg"]["app.pdo.cache"]))
        {
            $GLOBALS["cfg"]["app.pdo.cache"]->flush();
        }
        if (isset($_SESSION))
        {
            $_SESSION["pdo.cache"] = array();
        }
        if (isset($GLOBALS["pdo.cache"]))
        {
            $GLOBALS["pdo.cache"] = array();
        }
    }
}