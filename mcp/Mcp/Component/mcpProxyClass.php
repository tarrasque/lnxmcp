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
 * Description of mcpProxyClass
 *
 * @author andrea
 */
class mcpProxyClass
{
    //    public function apiCommon ($srvprc, $ispreload = false, $scopeIn = array (), $modinit = null, $subcall = null)

    public static function apiRemote(masterControlProgram &$mcp, $srvprc, array $scopeIn = array(), $modinit = null, $subcall = null, $vendor = null)
    {
        if (function_exists("curl_setopt") == false) {
            return $scopeIn;
        }
        $scopeOut = $scopeIn;
        $url = "";
        try {
            if (!isset($scopeIn["url"])) {
                if ($vendor == null) {
                    if ($mcp->getCfg("app.remote.url") == null) {
                        return $scopeOut;
                    } else {
                        $url = $mcp->getCfg("app.remote.url");
                    }
                } else {
                    $url = $vendor;
                    $vendor = null;
                }
            } else {
                $url = $scopeIn["url"];
            }
            if ($vendor != null) {
                $url .= "/" . $vendor;
            }
            if ($modinit != null) {
                $url .= "/" . $modinit;
            }
            if ($subcall != null) {
                $url .= "/" . $subcall;
            }
            $url .= "/" . $srvprc;
            $ch = curl_init();
            lnxmcp()->info("apiRemote=>url:".$url);
            curl_setopt($ch, CURLOPT_URL, $url);
            if (isset($scopeIn["proxy"])) {
                curl_setopt($ch, CURLOPT_PROXY, $scopeIn["proxy"]);
                if (isset($scopeIn["proxyUser"])) {
                    $proxyauth = $scopeIn["proxyUser"] . ":" . @$scopeIn["proxyPass"];
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);   // Use if proxy have username and password
                }
            }
            curl_setopt($ch, CURLOPT_HEADER, 0); // return headers 0 no 1 yes
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return page 1:yes
            curl_setopt($ch, CURLOPT_TIMEOUT, 200); // http request timeout 20 seconds
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects, need this if the url changes
            curl_setopt($ch, CURLOPT_MAXREDIRS, 2); //if http server gives redirection responce
            if (!isset($scopeIn["user_agent"])) {
                $scopeIn["user_agent"]="Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7";
            }
            lnxmcp()->info("apiRemote=>user_agent:".$scopeIn["user_agent"]);
            curl_setopt($ch, CURLOPT_USERAGENT, $scopeIn["user_agent"]);
            if (isset($scopeIn["cookiesfile"])) {
                curl_setopt($ch, CURLOPT_COOKIEJAR, $scopeIn["cookiesFile"]); // cookies storage / here the changes have been made
                curl_setopt($ch, CURLOPT_COOKIEFILE, $scopeIn["cookiesFile"]);
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // false for https
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $scopeIn);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_ENCODING, "gzip"); // the page encoding
            $scopeOut = json_decode(curl_exec($ch), 1); // execute the http request
            curl_close($ch); // close the connection
        } catch (\Exception $e) {
            $mcp->warning($e->getMessage());
            return $scopeOut;
        }
        return $scopeOut;
    }
    public static function apiShell(masterControlProgram &$mcp, $srvprc, array $scopeIn = array(), $modinit = null, $subcall = null, $vendor = null)
    {
        try {
            foreach ($scopeIn as $ek => $ev) {
                putenv($ek . "=" . $ev);
            }
            $cmd = "";
            if ($vendor != null) {
                if ($cmd != "") {
                    $cmd .= DIRECTORY_SEPARATOR;
                }
                $cmd .= $vendor;
            }
            if ($modinit != null) {
                if ($cmd != "") {
                    $cmd .= DIRECTORY_SEPARATOR;
                }
                $cmd .= $modinit;
            }
            if ($subcall != null) {
                if ($cmd != "") {
                    $cmd .= DIRECTORY_SEPARATOR;
                }
                $cmd .= $subcall;
            }
            if ($cmd != "") {
                $cmd .= DIRECTORY_SEPARATOR;
            }
            $cmd .= $srvprc;
            return shell_exec($cmd);
        } catch (\Exception $e) {
            $mcp->warning($e->getMessage());
            return $scopeIn;
        }
    }
    public static function blockShell(masterControlProgram &$mcp, $srvprc, array $scopeIn = array(), $modinit = null, $subcall = null, $vendor = null)
    {
        try {
            foreach ($scopeIn as $ek => $ev) {
                putenv($ek . "=" . $ev);
            }
            $cmd = "";
            if ($vendor != null) {
                if ($cmd != "") {
                    $cmd .= DIRECTORY_SEPARATOR;
                }
                $cmd .= $vendor;
            }
            if ($modinit != null) {
                if ($cmd != "") {
                    $cmd .= DIRECTORY_SEPARATOR;
                }
                $cmd .= $modinit;
            }
            if ($subcall != null) {
                if ($cmd != "") {
                    $cmd .= DIRECTORY_SEPARATOR;
                }
                $cmd .= $subcall;
            }
            if ($cmd != "") {
                $cmd .= DIRECTORY_SEPARATOR;
            }
            $cmd .= $srvprc;
            if (isset($scopeIn["MIME_TYPE"])){
                header('Content-type: '.$scopeIn["MIME_TYPE"]);
            }
            passthru($cmd);
        } catch (\Exception $e) {
            $mcp->warning($e->getMessage());
        }
    }

}