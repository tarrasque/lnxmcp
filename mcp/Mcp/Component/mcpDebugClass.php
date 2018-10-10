<?php
/**
 * LinHUniX Web Application Framework
 *
 * @author    Andrea Morello <andrea.morello@linhunix.com>
 * @copyright LinHUniX L.t.d., 2018, UK
 * @license   Proprietary See LICENSE.md
 * @version   GIT:2018-v2
 */

namespace LinHUniX\Mcp\Component;

use LinHUniX\Mcp\masterControlProgram;
use LinHUniX\Mcp\Provider\LoggerProviderModel;

/**
 * Description of mcpDebugClass
 *
 * @author andrea
 */
class mcpDebugClass
{
    private $mcp;

    /**
     * __construct
     *
     * @param  mixed $mcp
     *
     * @return void
     */
    public function __construct (masterControlProgram &$mcp)
    {
        $this->mcp =& $mcp;
        if ($this->getLogger () == null) {
            if ($mcp->getResource (masterControlProgram::CLASS_LOGGER) != null) {
                $mcp->register (new ${$mcp->getResource (masterControlProgram::CLASS_LOGGER)}());
            } else {
                $mcp->register (new LoggerProviderModel());
            }
        }
    }

    /**
     * debug
     *
     * @param  mixed $message
     *
     * @return void
     */
    public function debug ($message)
    {
        $this->getLogger ()->debug ($message);
    }

    /**
     * info
     *
     * @param  mixed $message
     *
     * @return void
     */
    public function info ($message)
    {
        $this->getLogger ()->info ($message);
    }

    /**
     * warning
     *
     * @param  mixed $message
     *
     * @return void
     */
    public function warning ($message)
    {
        $this->getLogger ()->warning ($message);
    }

    /**
     * error
     *
     * @param  mixed $message
     *
     * @return void
     */
    public function error ($message)
    {
        $this->getLogger ()->error ($message);
    }

    /**
     * critical
     *
     * @param  mixed $message
     *
     * @return void
     */
    public function critical ($message)
    {
        $this->getLogger ()->error ($message);
        $this->header ('Location: /500', true, true, 500);
    }

    /**
     * imhere
     *
     * @return void
     */
    public function imhere ()
    {
        if (function_exists ("debug_backtrace")) {
            $arr = debug_backtrace ();
        } else {
            array (1 => array ("file" => "none", "line" => 0));
        }
        if ($this->getRes ("debug") == true) {
            $this->debug ("[I am here]:" . $arr[1]['file'] . ":" . $arr[1]['line']);
            $imhere = "/tmp/" . $this->getRes ("def") . "imhere";
            if (!isset($GLOBALS["imhere"])) {
                $GLOBALS["imhere"] = array ();
                if (file_exists ($imhere)) {
                    eval(file_get_contents ($imhere));
                }
            }
            $GLOBALS["imhere"][$arr[1]['file']]++;
            file_put_contents ($imhere, "\$GLOBALS['imhere']=" . var_export ($GLOBALS["imhere"], 1) . ";");
        }
    }

    /**
     * webRem
     *
     * @param  mixed $message
     *
     * @return void
     */
    public function webRem ($message)
    {
        echo "\n<!-- ===========================================================\n";
        echo "====  LinHUniX :" . $message;
        echo "\n<=========================================================== !-->\n";
    }

    /**
     * webDump
     *
     * @param  mixed $message
     * @param  mixed $var
     *
     * @return void
     */
    public function webDump ($message, $var = null)
    {
        echo "\n<hr>\n";
        echo "<h2> LinHUniX :" . $message . "</h2>\n";
        echo "\n<hr>\n";
        if (!empty($var)) {
            echo "<pre>" . print_r ($var, 1) . "</pre>\n";
            echo "\n<hr>\n";
        }
    }

    /**
     * notFound
     *
     * @param  mixed $message
     *
     * @return void
     */
    public function notFound ($message)
    {
        $this->error ($message);
        $this->header ("HTTP/1.1 301 Moved Permanently");
        $this->header ('Location:/404', true); //, true, 404);
    }

    /**
     * move
     *
     * @param  mixed $string
     *
     * @return void
     */
    public function move ($string,$default=null,$ext="",$path=null,$andEnd=true)
    {
        if (empty($string)) {
            $this->critical ("Moving to Null Error");
        }
        $this->info ("moving to " . $string);
        if ($path==null){
            $path=$this->getRes ("path");
        }
        if ($default==null){
            $default=$string;
        }
        if (file_exists  ($path. $string.$ext)) {
            include $path.$string.$ext;
        } else if(file_exists  ($path. $default.$ext)) {
            include $path.$default.$ext;
        } else {
            $this->critical ("Moving to " . $path . $string . " Error file not found");
        }
        if ($andEnd==true){
            exit(0);
        }
    }

    /**
     * header
     *
     * @param  mixed $string
     * @param  mixed $end
     * @param  mixed $replace
     * @param  mixed $retcode
     *
     * @return void
     */
    public function header ($string, $end = false, $replace = true, $retcode = null)
    {
        $msg = " Not End";
        if ($end) {
            $msg = " With End";
        }
        $this->error ("Header [" . $retcode . "]:" . $string . $msg);
        \header ($string, $replace, $retcode);
        debug_print_backtrace ();
        if ($end) {
            exit(0);
        }
    }

    /**
     * getMCP
     *
     * @return void
     */
    private function getMCP ()
    {
        return $this->mcp;
    }

    /**
     * getCfg
     *
     * @param  mixed $string
     *
     * @return void
     */
    private function getRes ($string)
    {
        return $this->getMCP ()->geResource ($string);
    }

    /**
     * getLogger
     *
     * @return void
     */
    private function getLogger ()
    {
        return $this->getMCP ()->getCfg ("Logger");
    }
}
