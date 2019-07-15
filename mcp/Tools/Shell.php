<?php
/**
 * Created by PhpStorm.
 * User: linhunix
 * Date: 9/25/2018
 * Time: 2:55 PM.
 */
function mcpRunShell()
{
    global $app_path,$mcp_path,$argc,$argv,$cfg;
    lnxmcp()->setCfg('app.type', 'shell');
    if (file_exists($app_path.'/Header.txt')) {
        $content= file_get_contents($app_path.'/Header.txt');
        $content= str_replace("{{version}}",lnxMcpVersion(),$content);
        echo $content;
    }
    echo '';
    $help = array();
    $help['lnxmcp-mnu'] = "Run a Menu \n req arg: <menu name>";
    $help['lnxmcp-tag'] = "Run a Tag \n req arg: <tag name>";
    $help['lnxmcp-chk'] = "Run a Check\n  req arg: <check name>";
    $help['lnxmcp-cct'] = "Run a common controller\n  req arg: <common controller name> <module name>";
    $help['lnxmcp-ctl'] = "Run a user controller\n  req arg: < controller name> <module name>";
    $help['lnxmcp-cbl'] = "Run a common block\n  req arg: < block name> <module name>";
    $help['lnxmcp-dbj'] = "Run a db query json\n  req arg: < query name> <module name>";
    $help['lnxmcp-dbm'] = "Run a db migrate \n  req arg: < command name> <element name>";
    $help['lnxmcp-phr'] = "Generate a phar file of the progam\n req arg <type |shell>";
    if (isset($argv[1])) {
        lnxmcp()->debugVar('head-shell', 'argv', $argv);
        switch ($argv[1]) {
            case 'lnxmcp-mnu':
                lnxmcp()->runMenu($argv[2]);
                break;
            case 'lnxmcp-tag':
                lnxmcp()->runTag($argv[2]);
                break;
            case 'lnxmcp-chk':
                lnxmcpChk();
                break;
            case 'lnxmcp-dbm':
                lnxmcpDbM($argv[2], $argv[3]);
                break;
            case 'lnxmcp-cct':
                lnxmcp()->controllerCommon($argv[2], false, $argv, $argv[3]);
                break;
            case 'lnxmcp-ctl':
                lnxmcp()->controller($argv[2], false, $argv, $argv[3]);
                break;
            case 'lnxmcp-dbj':
                lnxmcp()->queryJsonR($argv[2], false, $argv, $argv[3]);
                break;
            case 'lnxmcp-cbl':
                lnxmcp()->showFullCommonBlock($argv[2], $argv, $argv[3]);
                break;
            case 'lnxmcp-dmp':
                // NOT PRESENT ON HELP FOR SECURITY QUESTION
                var_dump(lnxmcp());
                break;
            case 'lnxmcp-dcf':
                // NOT PRESENT ON HELP FOR SECURITY QUESTION
                var_export($cfg);
                break;
            case 'lnxmcp-cfg':
                // NOT PRESENT ON HELP FOR SECURITY QUESTION
                echo " the config is :\n";
                foreach (((array) $cfg) as $ik => $item) {
                    if (is_array($item)) {
                        echo "$ik is:\n";
                        foreach ($item as $cfgk => $cfgv) {
                            if (is_string($cfgv) || is_bool($cfgv) || is_int($cfgv)) {
                                echo '-->'.$cfgk.':'.var_export($cfgv, 1)."\n";
                            } else {
                                echo '-->'.$cfgk.": is set as object\n";
                            }
                        }
                    }
                }
                break;
            case 'lnxmcp-phr':
                if ($argv[2] == 'shell') {
                    LinHUniX\Mcp\Tools\pharizeShell::run();
                } else {
                    LinHUniX\Mcp\Tools\pharizeBase::run();
                }
                break;
            case 'help':
                echo "lnxmcp <action> <args.....>\n";
                if (file_exists($mcp_path.'/Help.txt')) {
                    echo file_get_contents($mcp_path.'/Help.txt');
                } else {
                    echo " the help is :\n\n";
                    foreach ($help as $hk => $hv) {
                        echo '[ '.$hk." ]:\n--- Desc:  ---\n".$hv."\n--- End Desc ---\n\n";
                    }
                }
                break;
            default:
                echo 'not valid argv';
                var_dump($argv);
        }
    }
}