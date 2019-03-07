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
class mcpMailClass
{
    public static function mailSimple($to, $from, $subject, $body, $html = false, $attachment = array())
    {
        try {
            $headers = "From: " . strip_tags($from) . "\r\n";
            $headers .= "Reply-To: " . strip_tags($form) . "\r\n";
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
            if ($html == true) {
                $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
                    "Content-Transfer-Encoding: 7bit\n\n" . $body . "\n\n";
            } else {
                $message = "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"UTF-8\"\n" .
                    "Content-Transfer-Encoding: 7bit\n\n" . $body . "\n\n";
            }
            if (!is_array($attachment)) {
                $attachment = array($attachment);
            }
            foreach ($attachment as $file) {
                if (file_exists($file)) {
                    $message .= "--{$mime_boundary}\n";
                    $fp =    @fopen($file, "rb");
                    $data =  @fread($fp, filesize($file));
                    @fclose($fp);
                    $data = chunk_split(base64_encode($data));
                    $message .= "Content-Type: application/octet-stream; name=\"" . basename($file) . "\"\n" .
                        "Content-Description: " . basename($file) . "\n" .
                        "Content-Disposition: attachment;\n" . " filename=\"" . basename($file) . "\"; size=" . filesize($file) . ";\n" .
                        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
                }
            }
            mail($to, $subject, $message, $headers);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    public static function mailService($page = null, $scopeIn = array(), $modinit = null)
    {
        lnxmcp()->info("MCP>>mail>>" . $page);
        try{
            if (!is_array($scopeIn)) {
                $scopeIn = array("In" => $scopeIn);
            }
            if (lnxmcp()->getCfg("app.Service.mail") != null) {
                if (($page != null) || ($page != "none") || ($page != ".")) {
                    $scopeIn["message"] = lnxmcp()->page($page, $scopeIn, $modinit, null, null, true);
                }
                lnxmcp()->Service("mail", false, $scopeIn);
            } else {
                $to = $scopeIn["to"];
                $from = $scopeIn["from"];
                $subject = $scopeIn["subject"];
                $message = $scopeIn["message"];
                $files = $scopeIn["files"];
                mcpMailClass::mailSimple($to,$from,$subject,$message,true,$files);
            }
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function  supportmail($message)
    {
        if (lnxmcp()->getResource("support.onerrorsend") == true) {
            $mailto = lnxmcp()->getResource("support.mail");
            if ($mailto != null && $mailto!=false) {
                try {
                    lnxmcp()->info("Prepare Mail Support to ".$mailto);
                    $from = "noreply.".lnxmcp()->getResource("def")."@localhost";
                    $subject = "Error Reporting - " . lnxmcp()->getResource("def") . " - " . date('d/m/y g:i a');
                            if (function_exists("debug_backtrace")) {
                        $message .= "\n\n" . print_r(debug_backtrace(), 1);
                    }
                    $message .= "\n\n" . print_r(lnxmcp(), 1);
                    if (lnxmcp()->getCfg("app.Service.mail") != null) {
                        lnxmcp()->debug("Prepare Mail Support (mcp) to ".$mailto);
                        $scopeIn=array(
                            "to"=>$mailto,
                            "from"=>$from,
                            "subject"=>$subject,
                            "message"=>$message
                        );
                        mcpMailClass::mailService($scopeIn);
                    }else{
                        lnxmcp()->debug("Prepare Mail Support (classic) to ".$mailto);
                        mcpMailClass::mailSimple($mailto,$form,$subject,$message,false);
                    }
                } catch (\Exception $e) {
                    lnxmcp()->warning("Support Mail Error:" . $e->getMessage());
                }
            }else{
                lnxmcp()->warning("No Support Mail Error!!");
            }
        }
    }
}
