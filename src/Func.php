<?php
////////////////////////////////////////////////////////////////////////////////
// ERROR/CONFIG
////////////////////////////////////////////////////////////////////////////////
function DumpAndExit ($message = "")
{
    $GLOBALS["mcp"]->info ("DumpAndExit:" . $message);
    foreach (debug_backtrace () as $row => $debug) {
        $GLOBALS["mcp"]->debug (implode ("|-|", $debug));
    }
    exit();
}

/**
 * this version has only the error log call because is work when is present a big issue
 * @param String $message
 * @param bool $exit
 */
function DumpOnFatal ($message, $exit = false)
{
    echo $message;
    foreach (debug_backtrace () as $errarr) {
        error_log ("-> " . $errarr["file"] . " : " . $errarr["line"] . " <br>");
    }
    foreach (get_included_files () as $filename) {
        error_log ("Load: $filename");
    }
    error_log ("FATAL ERROR - lnxmcp is NOT SETTED!!! ");
    error_log (debug_print_backtrace ());
    if ($exit == true) {
        exit(1);
    }
}

/**
 * A basic autoload implementation that should be compatible with PHP 5.2.
 *
 * @author pmg
 */
function legacyAutoload ($className)
{
    global $autoLoadFolders;
    $className = str_replace ('/LinHUniX/', '/', $className);
    foreach ($autoLoadFolders as $folder) {
        $classPath = $folder . DIRECTORY_SEPARATOR . $className . '.php';
        if (file_exists ($classPath)) {
            require_once $classPath;
            return true;
        }
    }
    return false;
}

/**
 * A basic autoload implementation that should be compatible with PHP 5.2.
 *
 * @author pmg
 */
function selfAutoLoad ($srcPath)
{

    global $autoLoadFolders;
    $srcPath = realpath ($srcPath);
    $scannedItems = scandir ($srcPath);
    foreach ($scannedItems as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        if (is_dir ($folder = $srcPath . DIRECTORY_SEPARATOR . $item)) {
            $autoLoadFolders[] = $folder;
        }
    }
    spl_autoload_register ('legacyAutoload', true/*, true*/);
}

function lnxmcp ()
{
    if (isset($GLOBALS["mcp"])) {
        return $GLOBALS["mcp"];
    } else {
        DumpOnFatal ("FATAL ERROR - lnxmcp is NOT SETTED!!! \n", true);
    }
}

function linhunixErrorHandlerDev ($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting () & $errno)) {
        return false;
    }
    $errtype = $errno;
    $exit = false;
    $drvlvl = 0;
    switch ($errno) {
        case E_ERROR:
            $exit = true;
        case E_USER_ERROR:
            lnxmcp ()->error ($errstr . "[" . $errfile . "] [" . $errline . "]");
            break;
        case E_USER_DEPRECATED:
        case E_WARNING:
        case E_USER_WARNING:
            lnxmcp ()->warning ($errstr . "[" . $errfile . "] [" . $errline . "]");
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $errtype = "INF";
            lnxmcp ()->info ($errstr . "[" . $errfile . "] [" . $errline . "]");
            break;
        default:
            $errtype = "DBG";
            lnxmcp ()->debug ($errstr . "[" . $errfile . "] [" . $errline . "]");
            break;
    }
    if ($exit) {
        \header ("HTTP/1.1 302 Moved Temporarily", true, 302);
        \header ('Location: /500', true, 500);
        exit(1);
        exit(1);
    }
    return true;
}

function mcpErrorHandlerInit ()
{
    $old_error_handler = set_error_handler ("linhunixErrorHandlerDev");
}