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

/**
 * Description of mcpToolsClass
 *
 * @author andrea
 */
class mcpToolsClass {

    /**
     * Clear Escape char 
     * @param String $string
     * @return String
     */
    public function escapeClear($string) {
        return preg_replace('~[\x00\x0A\x0D\x1A\x22\x27\x5C]~u', '\\\$0', $string);
    }

    /**
     * clear string from strange chars 
     * @param String $str
     * @return String
     */
    public function toAscii($str) {
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
        return $clean;
    }
    /**
     * Request save to session 
     * @param type $arguments name of the request 
     * @param type $onlyPost if true don-t read get 
     */
    public function Req2Session($arguments, $onlyPost = false) {
        if (!$onlyPost) {
            if (isset($_GET[$arguments])) {
                $_SESSION[$arguments] = $_GET[$arguments];
            }
        }
        if (isset($_POST[$arguments])) {
            $_SESSION[$arguments] = $_POST[$arguments];
        }
        if (!isset($_SESSION[$arguments])) {
            $_SESSION[$arguments] = "";
        }
        if ($_SESSION[$arguments] == "Reset") {
            $_SESSION[$arguments] = "";
            $_REQUEST[$arguments] = "";
            $_GET[$arguments] = "";
            $_POST[$arguments] = "";
        }
        if ($_SESSION[$arguments] == "") {
            $_SESSION[$arguments] = "";
            $_GET[$arguments] = "";
            $_POST[$arguments] = "";
        }
    }
}