<?php

namespace LinHUniX\Pdo\Driver;

use PDO;
use PDOException;
use Exception;
use LinHUniX\Mcp\Model\mcpBaseModelClass;

/*
 * @copyright Content copyright to linhunix.com 2003-2018
 * @author Andrea Morello <lnxmcp@linhunix.com>
 * @version GIT:2018-v1
 * this new class implement the PDO mode to connect on databases;
 * @see [vendor]/mcp/Head.php
 */

class pdoDriver extends mcpBaseModelClass
{
    /**
     * @var \PDO
     */
    public $PDO;
    public $tables;
    public $tabcount;
    public $tmp;
    public $cache;
    public $debug;
    public $database;
    public $dburlcon;
    public $lasterror;
    public $lastrows;
    /**
     * __construct.
     *
     * @param mixed $mcp
     * @param mixed $scopeCtl
     * @param mixed $scopeIn
     */
    public function __construct(\LinHUniX\Mcp\masterControlProgram &$mcp, array $scopeCtl, array $scopeIn)
    {
        parent::__construct($mcp, $scopeCtl, $scopeIn);
        $i = 0;
        if (isset($scopeIn['dburlcon'])) {
            $this->debug = false;
            $this->dburlcon = $scopeIn['dburlcon'];
            $this->database = $scopeIn['database'];
            $username = $scopeIn['username'];
            $password = $scopeIn['password'];
            $options = $scopeIn['options'];
            $this->getMcp()->info('dburlcon:'.$this->dburlcon);
            $this->getMcp()->debug('username:'.$username);
            $this->getMcp()->info('database:'.$this->database);
            try {
                $this->PDO = new PDO($this->dburlcon, $username, $password, $options);
            } catch (Exception $e) {
                $mcp->warning('DBCONN: ERR='.$e->getMessage());

                return null;
            }
            $data = $this->listTable();
            if (is_array($data)) {
                foreach ($data as $dt) {
                    foreach ($dt as $k => $v) {
                        $this->tables[$i] = $v;
                        ++$i;
                    }
                }
            }
        } else {
            $this->warning('Not Db Connection Found!!');

            return null;
        }
        $this->tabcount = $i;
        $this->tmp = array();
        $this->cache = array();
    }

    protected function listTable()
    {
        return $this->getTable('SHOW TABLES');
    }

    /**
     * Check if pdo is live.
     *
     * @return bool status
     */
    public function isLive()
    {
        if ($this->PDO != null) {
            return true;
        }

        return false;
    }

    /*
     * real_escape_string
     * remplace the mysqlLegacyRealEscapeString
     * for an issue on Php 5.6 change to
     *
     * @see mysqlLegacyRealEscapeString(),PDO->quote()
     *
     * @param  mixed $value
     *
     * @return string
     */
    public function real_escape_string($value,$oldmethod=true)
    {
        if ($oldmethod==true){
            $search = array('\\',  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
            $replace = array('\\\\', '\\0', '\\n', '\\r', "\'", '\"', '\\Z');
            return str_replace($search, $replace, $value);
        }
        return $this->PDO->quote($value);
    }

    /**
     * table_exist
     * Check if the table exists in the database.
     *
     * @param mixed $tablename
     *
     * @return bool
     */
    public function table_exist($tablename)
    {
        if (in_array($tablename, $this->tables)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * intexec  Direct PDO execution.
     *
     * @param mixed $query
     * @param mixed $noupdate error 
     * @return any
     */
    public function intexec($query, $noupdate = false)
    {
        try {
            if ($this->PDO == null) {
                $this->getMcp()->warning($this->database.':is null!!');

                return false;
            }

            $res = $this->PDO->exec($query);
            $this->getMcp()->debug($this->database.':return row update='.print_r($res, 1));
            if ($noupdate == false and $res > 0) {
                return true;
            }
            if ($noupdate == true and $res >= 0) {
                return true;
            }
            $this->getMcp()->warning($this->database.':row='.print_r($res, 1));
            $this->getMcp()->warning($this->database.':wrn='.print_r($this->PDO->errorInfo(),1));
            return false;
        } catch (PDOException $pe) {
            $this->getMcp()->warning($this->database.':PDO ERR'.$pe->getMessage());
            $this->lasterror=$this->PDO->errorInfo();
            return false;
        } catch (Exception $e) {
            $this->getMcp()->warning($this->database.':GEN ERR'.$e->getMessage());
            $this->lasterror=$e->getMessage();
            return false;
        }
    }
  /**
     * intexec  Direct PDO execution.
     *
     * @param mixed $query
     *
     * @return any
     */
    public function rawexec($sql,$noupdate=false){
        $this->getMcp()->debug('queryIn:'.$this->database.'='.$sql);
        return $this->intexec($sql,$noupdate);
    }
  
    /**
     * execute.
     *
     * @param mixed $sql
     * @param mixed $var
     * @param mixed $noupdate error 
     * @param mixed $nopqute issue mssql
     *
     * @return bool
     */
    public function execute($sql, $var = array(), $noupdate = false,$noquot=true)
    {
        $this->getMcp()->debug('queryIn:'.$this->database.'='.$sql);
        if (isset($var['WHERE'])) {
            $sql = str_replace('[WHERE]', $var['WHERE'], $sql);
            unset($var['WHERE']);
        }
        foreach ($var as $k => $v) {
            $sql = str_replace('['.$k.']', $this->real_escape_string($v), $sql);
            $sql = str_replace('[R:'.$k.']', $v, $sql);
            $sql = str_replace('[S:'.$k.']', \stripslashes($v), $sql);
        }
        if ($noquot){
            $sql = str_replace("'", '"', $sql);
        }
        $this->getMcp()->debug('queryOut:'.$this->database.'='.$sql);
        if ($this->intexec($sql, $noupdate) == false) {
            $this->getMcp()->warning('[KO]'.$this->database.'='.$sql);
            if ($this->PDO != null) {
                $this->lasterror=$this->PDO->errorInfo();
                $this->getMcp()->warning('[KO]'.$this->database.': '.print_r($this->lasterror, 1));
            } else {
                $this->getMcp()->warning('[KO]'.$this->database.': PDO IS NULL!!!');
            }

            return false;
        }
        $this->getMcp()->debug('[OK]'.$this->database.'='.$sql);

        return true;
    }

    /**
     * executeWithRollback use the conventional pdo transaction esectution.
     *
     * @param mixed $sql
     * @param mixed $var
     *
     * @return array
     */
    public function executeWithRollback($sql, $var = array())
    {
        $this->getMcp()->debug('queryIn:'.$this->database.'='.$sql);
        if (isset($var['WHERE'])) {
            $sql = str_replace('[WHERE]', $var['WHERE'], $sql);
            unset($var['WHERE']);
        }
        foreach ($var as $k => $v) {
            $sql = str_replace('['.$k.']', $this->real_escape_string($v), $sql);
            $sql = str_replace('[R:'.$k.']', $v, $sql);
            $sql = str_replace('[S:'.$k.']', \stripslashes($v), $sql);
        }
        $this->getMcp()->debug('queryOut:'.$this->database.'='.$sql);
        if ($this->PDO == null) {
            return false;
        }
        $res = array();
        try {
            $stmt = $this->PDO->prepare($sql);
            $this->PDO->beginTransaction();
            $stmt->execute($var);
            $this->PDO->commit();
            $res = $this->PDO->lastInsertId();
        } catch (Exception $e) {
            $this->PDO->rollback();
            $this->getMcp()->warning('[KO]'.$this->database.'='.$sql.':'.$e->getMessage());
            $this->lasterror=$this->PDO->errorInfo();
            $this->getMcp()->warning('[KO]'.$this->database.'='.$sql.':'.\print_r($this->lasterror,1));
            return false;
        }

        return $res;
    }

    /**
     * queryReturnResultSet Get Database Query Results.
     *
     * @param mixed $sql
     * @param mixed $logging
     *
     * @return statement
     */
    public function queryReturnResultSet($sql, $logging = false)
    {
        if ($this->debug) {
            list($usec, $sec) = explode(' ', microtime());
            $starttime = ((float) $usec + (float) $sec);
        }
        if ($this->PDO == null) {
            return false;
        }
        try {
            $statement = $this->PDO->query($sql);
        } catch (PDOException $pe) {
            $this->getMcp()->warning($this->database.$pe->getMessage());
            $this->lasterror=$this->PDO->errorInfo();
            return null;
        } catch (Exception $e) {
            $this->getMcp()->warning($this->database.$e->getMessage());
            $this->lasterror=$e->getMessage();
            return null;
        }
        if ($this->debug) {
            list($usec, $sec) = explode(' ', microtime());
            $finishtime = ((float) $usec + (float) $sec);
            $this->querycache($sql, $starttime, $finishtime);
        }

        return $statement;
    }

    /**
     * simpleQuery.
     *
     * @param mixed $sql
     * @param mixed $var
     * @param mixed $err
     * @param mixed $fetchmode
     *
     * @return array
     */
    public function simpleQuery($sql, $var = array(), $err = true, $fetchmode=null)
    {
        $result_set = array();
        if ($fetchmode==null){
            $fetchmode=PDO::FETCH_BOTH;
        }
        $this->getMcp()->debug('queryIn:'.$this->database.'='.$sql);
        if (isset($var['WHERE'])) {
            $sql = str_replace('[WHERE]', $var['WHERE'], $sql);
            unset($var['WHERE']);
        }
        foreach ($var as $k => $v) {
            $sql = str_replace('['.$k.']', $this->real_escape_string($v), $sql);
            $sql = str_replace('[R:'.$k.']', $v, $sql);
            $sql = str_replace('[S:'.$k.']', \stripslashes($v), $sql);
        }
        $this->getMcp()->debug('queryOut:'.$this->database.'='.$sql);
        try {
            $statement = $this->queryReturnResultSet($sql);
            if ($statement == null) {
                if ($err == false) {
                    $this->getMcp()->critical($this->database.':'.$sql.' NULL DATA!!!');
                } else {
                    $this->getMcp()->warning($this->database.':'.$sql.' NULL DATA!!!');
                }

                return false;
            } else {
                while ($row = $statement->fetch($fetchmode)) {
                    $result_set[] = $row;
                }
            }
            @$statement->closeCursor();
        } catch (Exception $e) {
            if ($err == false) {
                $this->getMcp()->critical($this->database.$e->getMessage());
            } else {
                $this->getMcp()->warning($this->database.$e->getMessage());
            }
        }
        if ($result_set == null) {
            return false;
        }

        return $result_set;
    }
    /**
     * simpleQuery.
     *
     * @param mixed $sql
     * @param mixed $var
     * @param mixed $err
     *
     * @return array
     */
    public function queryLabel($sql, $var = array(), $err = true){
        $this->simpleQuery($sql,$var,$err,PDO::FETCH_ASSOC);
    }
    /**
     * simpleQuery.
     *
     * @param mixed $sql
     * @param mixed $var
     * @param mixed $err
     *
     * @return array
     */
    public function queryNoLabel($sql, $var = array(), $err = true){
        $this->simpleQuery($sql,$var,$err,PDO::FETCH_NUM);
    }

    /**
     * indexed query function.
     *
     * @param string $sql
     * @param string $idfield default = 'id'
     * @param bool   $sort    default = true
     *
     * @return array dataresult with index as $idfield
     */
    public function indexQuery($sql, $idfield = 'id', $sort = true)
    {
        $getData = $this->simpleQuery($sql);
        $resData = array();
        if (is_array($getData)) {
            foreach ($getData as $row) {
                $resData[$row[$idfield]] = $row;
            }
        }
        if ($sort == true) {
            ksort($resData);
        }

        return $resData;
    }

    /**
     * simpleCount.
     *
     * @param mixed $sql
     * @param mixed $var
     * @param mixed $err
     *
     * @return number
     */
    public function simpleCount($sql, $var = array(), $err = true)
    {
        $res = $this->simpleQuery($sql, $var, $err);

        return count($res);
    }

    /**
     * getTable
     * simpleQuery alias.
     *
     * @param mixed $sql
     * @param mixed $logging
     *
     * @return array
     */
    public function getTable($sql, $var = array(), $err = true)
    {
        return $this->simpleQuery($sql, $var, $err);
    }

    /**
     * getTable
     * simpleQuery alias.
     *
     * @param mixed $sql
     * @param mixed $logging
     *
     * @return array
     */
    public function data_table($sql, $var = array(), $err = true)
    {
        return $this->simpleQuery($sql, $var, $err);
    }

    /**
     * dataWalk  (use array_walk on result ).
     *
     * @param mixed $sql
     * @param mixed $callback
     * @param mixed $var
     * @param mixed $funarr
     * @param mixed $err
     *
     * @return any
     */
    public function dataWalk($sql, $callback, $var = array(), &$funarr = array(), $err = true)
    {
        $res = $this->data_table($sql, $var, $err);
        if (is_array($res)) {
            return array_walk($res, $callback, $funarr);
        }

        return false;
    }

    /**
     * firstRow show only the first record.
     *
     * @param mixed $sql
     * @param mixed $var
     * @param mixed $err
     *
     * @return array
     */
    public function firstRow($sql, $var = array(), $err = true)
    {
        $rs = $this->simpleQuery($sql, $var, $err);
        if ((!is_array($rs)) || (!isset($rs[0]))) {
            $this->getMcp()->warning($this->database.' Data is Null');

            return false;
        }

        return $rs[0];
    }

    /**
     * function firstRowField
     * reorder the result by the id.
     *
     * @param string $sql
     * @param string $idfield default='id'
     *
     * @return string/null
     */
    public function firstRowField($sql, $idfield = 'id')
    {
        $getData = $this->firstRow($sql);
        if (isset($getData[$idfield])) {
            return $getData[$idfield];
        }

        return null;
    }

    /**
     * data_row (alias)
     * firstRow show only the first record.
     *
     * @param mixed $sql
     * @param mixed $var
     * @param mixed $err
     *
     * @return array
     */
    public function data_row($sql, $var = array(), $err = true)
    {
        return $this->firstRow($sql);
    }

    //Delete Row from table @need review
    public function delRow($_table, $_id)
    {
        if (($_table != '') && ($_id > 0)) {
            $_result = $this->execute('DELETE FROM '.$_table.' WHERE id = '.$_id);
        }
        if ($_result) {
            return true;
        }

        return false;
    }

    /**
     * getRow.
     *
     * Get Row from table
     *
     * @todo need review
     *
     * @param mixed  $_fields
     * @param string $_table
     * @param string $_id
     * @param string $_id_field
     *
     * @return array
     */
    public function getRow($fields, $table, $refid, $idfield = 'id', $compare = '=')
    {
        if (empty($refid)) {
            return null;
        }
        $result = array();

        $row = $this->firstRow('SELECT * FROM '.$table.' WHERE '.$idfield.' '.$compare.' '.$refid.' LIMIT 1 ;');
        if (!is_array($row)) {
            return null;
        }
        if ($fields == null) {
            return $row;
        }
        if (!is_array($fields)) {
            return stripslashes($row[$fields]);
        }
        foreach ($fields as $field) {
            $result[$field] = stripslashes($row[$field]);
        }

        return $result;
    }

    //Get Rows from table

    /**
     * getRows.
     *
     * @todo need review
     *
     * @param mixed $_fields
     * @param mixed $_table
     * @param mixed $_order_by
     * @param mixed $_from
     * @param mixed $_size
     * @param mixed $_where_stmt
     * @param mixed $id
     *
     * @return array
     */
    public function getRows($_fields, $_table, $_order_by, $_from, $_size, $_where_stmt = '', $id = 'id')
    {
        $_result = array();
        $_cnt = 0;
        $_where_stmt = $_where_stmt != '' ? ' WHERE '.$_where_stmt : '';
        $_res = $this->simpleQuery('SELECT * FROM `'.$_table.'` '.$_where_stmt.' ORDER BY '.$_order_by.' LIMIT '.$_from.', '.$_size.';');
        foreach ($_res as $_cnt => $_row) {
            if (isset($_row[$id])) {
                $_cnt = $_row[$id];
            }
            foreach ($_fields as $_field) {
                $_result[$_cnt][$_field] = stripslashes($_row[$_field]);
            }
        }

        return $_result;
    }
    /**
     * return the last error on pdo
     * @return mixed array/string
     */
    public function getLastError(){
        return $this->lasterror;
    }

    //Get Last Run id from PDO

    /**
     * getLastId.
     *
     * @param mixed $table
     * @param mixed $id
     *
     * @return any
     */
    public function getLastRun()
    {
        $res = array();
        if ($this->PDO == null) {
            return false;
        }
        try {
            $res = $this->PDO->lastInsertId();
            if (!empty($res)) {
                return $res;
            }
        } catch (\Exception $e) {
            lnxmcp()->warning('PDOgetLastId:err:'.$e->getMessage());

            return null;
        }
    }

    //Get Last ID from table

    /**
     * getLastId.
     *
     * @param mixed $table
     * @param mixed $id
     *
     * @return any
     */
    public function getLastId($table, $id = 'id')
    {
        $res = $this->firstRow('SELECT `'.$id.'` FROM `'.$table.'` ORDER BY `'.$id.'` DESC ');
        if (isset($res[$id])) {
            return $res[$id];
        }

        return null;
    }

    /**
     * setRow Add/Update Row.
     *
     * @param mixed $_fields
     * @param mixed $_table
     * @param mixed $run
     */
    public function setRow($_fields, $_table, $run = true, $emptyval = false,$idvar = 'id')
    {
        if ((count($_fields) > 0) && ($_table != '')) {
            $_stmt = $this->getSql($_fields, $_table, $emptyval,$idvar);
            if ($run) {
                $_result = $this->executeWithRollback($_stmt);
            } else {
                $_result = $_stmt;
            }
        }
        if ($_result) {
            return $_result;
        }
        return true;
    }

    //GET SetRow SQL//todo need review
    public function getSql($_fields, $_table, $emptyval = false,$idvar = 'id')
    {
        $_stmt = '';
        if ($_fields[$idvar] > 0) {
            $_stmt .= 'UPDATE `'.$_table.'` SET ';
            foreach ($_fields as $_key => $_val) {
                if (($_val != '') or ($emptyval == true)) {
                    $_stmt .= '`'.$_key.'` = \''.addslashes($_val).'\',';
                }
            }
            $_stmt = substr($_stmt, 0, strlen($_stmt) - 1);
            $_stmt .= ' WHERE '.$idvar.' = '.$_fields[$idvar].';';
        } else {
            $_stmt = 'INSERT INTO `'.$_table.'` ( ';
            foreach ($_fields as $_key => $_val) {
                if ($_val != '') {
                    $_stmt .= '`'.$_key.'`,';
                }
            }
            $_stmt = substr($_stmt, 0, strlen($_stmt) - 1);
            $_stmt .= ' ) VALUES ( ';

            foreach ($_fields as $_key => $_val) {
                if ($_val != '') {
                    $_stmt .= '\''.addslashes($_val).'\',';
                }
            }
            $_stmt = substr($_stmt, 0, strlen($_stmt) - 1);
            $_stmt .= ' );';
        }
        return $_stmt;
    }

    /**
     * function getNames
     * reorder the result by the id
     * create 21/10/2019 - Andrea M. as ft compatibilty logic.
     *
     * @todo need review
     *
     * @param string $dstname
     * @param string $table
     * @param string $srcname
     * @param string $srcvalue
     *
     * @return string/boolean
     */
    public function getNames($dstname, $table, $srcname, $srcvalue)
    {
        $row = $this->firstRow('SELECT '.$dstname.' FROM '.$table.' WHERE '.$srcname.' = \' '.$srcvalue.' \' ');
        if (isset($row[$dstname])) {
            return $row[$dstname];
        }

        return false;
    }

    /**
     * $scope array is
     * var ["T"]:
     *  e  = execute : exec query with boolean results
     *  er  = execute with rollback : exec query with boolean results
     *  f  = firstRow : return only first row
     *  q  = retrive array of all results
     *  c  = return the count of the results
     *  s  = return the sql information
     *  r  = return sql and env
     *  other case return false;
     * var ["Q"] = query
     * var ["V"] = contain the values that need to remplace on query scripts.
     *
     * @author Andrea Morello <lnxmcp@linhunix.com>
     *
     * @version GIT:2018-v1
     *
     * @param Container $GLOBALS["cfg"] Dipendecy injection for Pimple\Container
     * @param array     $this->argIn    temproraney array auto cleanable
     *
     * @return bool|array query results
     */
    public function moduleCore()
    {
        if ($this->PDO == null) {
            $this->getMcp()->warning('Database Connection Error (not initalizzed!!)');

            return false;
        }
        if ((empty($this->argIn['Q']))) {
            return false;
        }
        try {
            switch ($this->argIn['T']) {
                case 'e':
                    $this->argOut = $this->execute($this->argIn['Q'], $this->argIn['V']);
                    break;
                case 'er':
                    $this->argOut = $this->executeWithRollback($this->argIn['Q'], $this->argIn['V']);
                    break;
                case 'f':
                    $this->argOut = $this->firstRow($this->argIn['Q'], $this->argIn['V']);
                    break;
                case 'q':
                    $this->argOut = $this->simpleQuery($this->argIn['Q'], $this->argIn['V']);
                    break;
                case 'c':
                    $this->argOut = $this->simpleCount($this->argIn['Q'], $this->argIn['V']);
                    break;
                case 's':
                    $this->argOut = $this->argIn['Q'];
                    break;
                case 'r':
                    $this->argOut = array(
                        'sql' => $this->argIn['Q'],
                        'env' => $this->argIn['E'],
                    );
                    break;
            }
        } catch (\Exception $e) {
            $this->getMcp()->warning('QueryIdx:Index='.$this->argIn['I'].',Error:'.$e->getMessage());
        }
    }

    //////////////////////////////////////////////////////////////////////////////
    // OLD STYLE COMPATIBILTY
    //////////////////////////////////////////////////////////////////////////////

    /**
     * query
     * simpleQuery alias.
     *
     * @param mixed $sql
     * @param mixed $logging
     *
     * @return array
     */
    public function query($sql, $logging = false)
    {
        $this->getMcp()->warning(" please don't use this method !! query ");
        $this->tmp['Data'] = $this->simpleQuery($sql);
        $this->tmp['DSrc'] = $sql;
        $this->tmp['RMin'] = 0;
        $this->tmp['RMax'] = count($this->tmp['Data']);
        $this->getMcp()->debug('query:'.print_r($this->tmp, 1));

        return $this->tmp['Data'];
    }

    //Get Insert ID - CHECK - function below insertID()
    public function lastData()
    {
        return $this->tmp['Data'];
    }

    //Get Next Database Record
    public function nextRow(&$statement = false)
    {
        $this->getMcp()->warning(" please don't use this method !! nextRow ");
        if ($statement == false) {
            if (!isset($this->tmp['Data'])) {
                $this->getMcp()->warning($this->database.' Data is Null');

                return false;
            }
            if (!is_array($this->tmp['Data'])) {
                $this->getMcp()->warning($this->database.' Data not array');

                return $this->tmp['Data'];
            }
            $rmin = $this->tmp['RMin'];
            if ($rmin > $this->tmp['RMax']) {
                $this->getMcp()->warning($this->database.' Data RMin over RMax');

                return false;
            }
            ++$rmin;
            $this->tmp['RMin'] = $rmin;
            $this->getMcp()->debug('next_record:'.print_r($this->tmp, 1));

            return $this->tmp['Data'][$rmin];
        }
        if (is_array($statement)) {
            return next($statement);
        }

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function next_record(&$statement = false)
    {
        $this->getMcp()->warning(" please don't use this method !! next_record ");

        return $this->nextRow($statement);
    }

    //Seek Database Results
    public function seek($position = 0, $statement = false)
    {
        $this->getMcp()->warning(" please don't use this method !! seek ");
        if ($statement == false) {
            if (!isset($this->tmp['Data'])) {
                $this->getMcp()->warning($this->database.'Data not array');

                return false;
            }
            if ($position > $this->tmp['RMax']) {
                $this->getMcp()->warning($this->database.'Data RMin over RMax');

                return false;
            }
            $this->tmp['RMin'] = $position;
            $this->getMcp()->warning($this->database.'query:'.print_r($this->tmp, 1));

            return $this->tmp['Data'][$position];
        }
        if (is_array($statement)) {
            return $statement[$position];
        }

        return $statement->fetch(PDO::FETCH_ASSOC, $position);
    }

    public function freeresult($statement = false)
    {
        $this->getMcp()->warning(" please don't use this method !! freeresult ");
        if ($statement != false) {
            $statement->closeCursor();
        }
        $this->tmp = array();
    }

    //Get Number Database Rows - CHECK - function below num_rows()
    public function numRows($statement = false)
    {
        $this->getMcp()->warning(" please don't use this method !! numRows ");
        if ($statement === false) {
            if (!isset($this->tmp['RMax'])) {
                $this->getMcp()->warning($this->database.' Data not array');

                return false;
            }

            return $this->tmp['RMax'];
        }
        if (is_array($statement)) {
            return count($statement);
        }
        try {
            if (method_exists($statment, 'fetchColumn')) {
                return $statement->fetchColumn();
            }
        } catch (Exception $e) {
            $this->getMcp()->warning($this->database.$e->getMessage());
        }

        return 0;
    }

    //Get Affected Database Rows
    public function affectedRows($resultSet = false)
    {
        $this->getMcp()->warning(" please don't use this method !! affectedRows ");

        return $this->numRows($resultSet);
    }

    //Get Number Database Rows
    public function num_rows($resultSet = false)
    {
        $this->getMcp()->warning(" please don't use this method !! num_rows ");

        return $this->numRows($resultSet);
    }
    function close(){
        $this->PDO = null;
    }
}
