<?php

/**
 * LinHUniX Web Application Framework.
 *
 * @author Andrea Morello <lnxmcp@linhunix.com>
 * @copyright LinHUniX L.t.d., 2018, UK
 * @license   Proprietary See LICENSE.md
 *
 * @version GIT:2018-v2
 */

namespace LinHUniX\Gfx\Service;

use LinHUniX\Mcp\Model\mcpBaseModelClass;
use LinHUniX\Mcp\masterControlProgram;

class gfxService extends mcpBaseModelClass
{
    private static $html2txt = null;
    private static $zebraimage = null;

    /**
     * @param array (reference of) $scopeCtl => calling Controlling definitions
     * @param array (reference of) $scopeIn  temproraney array auto cleanable
     */
    public function __construct(masterControlProgram &$mcp, array $scopeCtl, array $scopeIn)
    {
        parent::__construct($mcp, $scopeCtl, $scopeIn);
        $gfxdef = $this->getMcp()->getResource('gfx.default');
        if (!empty($gfxdef)) {
            if (is_array($gfxdef)) {
                foreach ($gfxdef as $defpack) {
                    $this->loadMenusCommon('Gfx/'.$defpack.'/mnu/default');
                    $this->loadTagsCommon('Gfx/'.$defpack.'/tag/default');
                }
            } else {
                $this->loadMenusCommon('Gfx/'.$defpack.'/mnu/default');
                $this->loadTagsCommon('Gfx/'.$defpack.'/tag/default');
            }
        }
    }

    private function getInternalPath()
    {
        try {
            if ($this->getMcp()->getCfg("phar")==true) {
                return str_replace(array("\"","\'"),"",$this->getMcp()->getCfg("purl")."mcp/");
            } else {
                return $this->getMcp()->getCfg('mcp.path');
            }
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * getHtml2Txt.
     *
     * @param string $source    html content
     * @param string $from_file
     *
     * @return string text converted
     */
    public function getHtml2Txt($source = '', $from_file = false)
    {
        if (self::$html2txt == null) {
            include_once __DIR__.'./../Component/html2text.class.php';
            self::$html2txt = new \LinHUniX\Gfx\Component\html2text();
        }
        self::$html2txt->set_html($source, $from_file);
        self::$html2txt->set_base_url();

        return self::$html2txt->get_text();
    }

    private function getZebraImageError($error_code)
    {
        $source_path = self::$zebraimage->source_path;
        $target_path = self::$zebraimage->target_path;
        $res = '';
        // if there was an error, let's see what the error is about
        switch ($error_code) {
        case 1:
            $res = 'Source file "'.$source_path.'" could not be found!';
            break;
        case 2:
            $res = 'Source file "'.$source_path.'" is not readable!';
            break;
        case 3:
            $res = 'Could not write target file "'.$source_path.'"!';
            break;
        case 4:
            $res = $source_path.'" is an unsupported source file format!';
            break;
        case 5:
            $res = $target_path.'" is an unsupported target file format!';
            break;
        case 6:
            $res = 'GD library version does not support target file format!';
            break;
        case 7:
            $res = 'GD library is not installed!';
            break;
        case 8:
            $res = '"chmod" command is disabled via configuration!';
            break;
        }

        return $res;
    }

    /**
     * getZebraImage.
     *
     *
     * @param string $source_file image input file
     * @param string $dest_file   image output file
     * @param string $action      request image action :
     *                            - "resize" resize image
     *                            -- args:width,height
     *                            - "flip_horizontal" invert horizontal image
     *                            - "flip_vertical" invert vertical image
     *                            - "flip_both" invert vertical and horizontal image
     *                            - "crop" cut image
     *                            -- args:start_x,start_y,end_x,end_y
     *                            - "rotate" rotate image
     *                            -- args:angle
     *
     * @return bool status
     */
    public function getZebraImage($action, $source_file, $dest_file, $arg = array())
    {
        lnxmcp()->info('getZebraImage');
        $rmsg = 'DONE';
        if (self::$zebraimage == null) {
            include_once __DIR__.'./../Component/zebra_image.class.php';
            self::$zebraimage = new \LinHUniX\Gfx\Component\zebra_image();
        }
        self::$zebraimage->source_path = $source_file;
        self::$zebraimage->target_path = $dest_file;
        switch ($action) {
        case 'resize':
            lnxmcp()->info('getZebraImage:resize');
            if (!self::$zebraimage->resize($arg['width'], $arg['height'], ZEBRA_IMAGE_BOXED, -1)) {
                $rmsg = $this->getZebraImageError(self::$zebraimage->error);
                lnxmcp()->warning('getZebraImage:error='.$rmsg);

                return false;
            }

            return true;
        case 'flip_horizontal':
            lnxmcp()->info('getZebraImage:flip_horizontal');
            if (!self::$zebraimage->flip_horizontal()) {
                $rmsg = $this->getZebraImageError(self::$zebraimage->error);
                lnxmcp()->warning('getZebraImage:error='.$rmsg);

                return false;
            }

            return true;
        case 'flip_vertical':
            lnxmcp()->info('getZebraImage:flip_vertical');
            if (!self::$zebraimage->flip_vertical()) {
                $rmsg = $this->getZebraImageError(self::$zebraimage->error);
                lnxmcp()->warning('getZebraImage:error='.$rmsg);

                return false;
            }

            return true;
        case 'flip_both':
            lnxmcp()->info('getZebraImage:flip_both');
            if (!self::$zebraimage->flip_both()) {
                $rmsg = $this->getZebraImageError(self::$zebraimage->error);
                lnxmcp()->warning('getZebraImage:error='.$rmsg);

                return false;
            }

            return true;
        case 'crop':
            lnxmcp()->info('getZebraImage:crop');
            if (!self::$zebraimage->crop($arg['start_x'], $arg['start_y'], $arg['end_x'], $arg['end_y'])) {
                $rmsg = $this->getZebraImageError(self::$zebraimage->error);
                lnxmcp()->warning('getZebraImage:error='.$rmsg);

                return false;
            }

            return true;
        case 'rotate':
            lnxmcp()->info('getZebraImage:rotate');
            if (!self::$zebraimage->rotate($arg['angle'])) {
                $rmsg = $this->getZebraImageError(self::$zebraimage->error);
                lnxmcp()->warning('getZebraImage:error='.$rmsg);

                return false;
            }
            $this->argOut['msg'] = $rmsg;

            return true;
        }
    }

    /**
     * callStaticCommon.
     *
     * @param string $InternalSource content
     * @param string $MimeType
     * @param bool   $ConverTag
     * @param array  $arg
     */
    public function callStaticCommon($InternalSource, $MimeType, $ConverTag = false, $arg = array())
    {
        try {
            $purl = $this->getInternalPath();
            $res = file_get_contents($purl.DIRECTORY_SEPARATOR.$InternalSource);
            \header('Content-type: '.$MimeType);
            if ($ConverTag == true) {
                echo $this->getMcp()->covertTag($res, $arg);
            } else {
                echo $res;
            }
        } catch (\Exception $e) {
            $this->getMcp()->error('Gfx->callStaticCommon:'.$e->getMessage());
            $this->getMcp()->NotFound($InternalSource);
        }
    }

    /**
     * callDynamicCommon.
     *
     * @param string $InternalSource content
     * @param string $MimeType
     * @param bool   $ConverTag
     * @param array  $arg
     */
    public function callDynamicCommon($InternalSource, $MimeType, $ConverTag = false, $arg = array())
    {
        try {
            $purl = $this->getInternalPath();
            ob_start();
            include $purl.$InternalSource.'.tpl';
            $res = ob_get_clean();
            \header('Content-type: '.$MimeType);
            if ($ConverTag == true) {
                echo $this->getMcp()->converTag($res, $arg);
            } else {
                echo $res;
            }
        } catch (\Exception $e) {
            $this->getMcp()->error('Gfx->callDynamicCommon:'.$e->getMessage());
            $this->getMcp()->NotFound($InternalSource);
        }
    }

    /**
     * loadMenusCommon.
     *
     * @param string $InternalSource content
     */
    public function loadMenusCommon($InternalSource)
    {
        try {
            $purl = $this->getInternalPath();
            $res = json_decode(file_get_contents($purl.$InternalSource.'.mnu.json'), true);
            if (is_array($res)) {
                foreach ($res as $menu => $sequence) {
                    $this->getMcp()->Debug('loadTagsCommon load menu:'.$menu);
                    $this->getMcp()->setCfg('app.menu.'.$menu, $sequence);
                }
            }
        } catch (\Exception $e) {
            $this->getMcp()->error('Gfx->loadMenusCommon:'.$e->getMessage());
            $this->getMcp()->NotFound($InternalSource);
        }
    }

    /**
     * loadTagsCommon.
     *
     * @param string $InternalSource content
     */
    public function loadTagsCommon($InternalSource)
    {
        try {
            $purl = $this->getInternalPath();
            $res = json_decode(file_get_contents($purl.$InternalSource.'.tag.json'), true);
            if (is_array($res)) {
                foreach ($res as $tag => $sequence) {
                    $this->getMcp()->Debug('loadTagsCommon load tag:'.$tag);
                    $this->getMcp()->setCfg('app.tag.'.$tag, $sequence);
                }
            }
        } catch (\Exception $e) {
            $this->getMcp()->error('Gfx->loadMenusCommon:'.$e->getMessage());
            $this->getMcp()->NotFound($InternalSource);
        }
    }

    /**
     * $scopeIN array is
     * var "[T]"
     * -- H2T =html to Text convert
     *      var ["html"] = stored in globals
     *      return txt converson
     * -- INT =Internal Static Source
     *      var ["source"] = Internal Source File
     *      var ["minetype"] = Mime Type of Internal Source File
     *      var ["tag"] = Use Tag Converter
     *      return txt converson
     * -- DYN =Internal dynamic Source
     *      var ["source"] = Internal Source File
     *      var ["minetype"] = Mime Type of Internal Source File
     *      var ["tag"] = Use Tag Converter
     *      return txt converson
     * -- MNU =Load common menu and tag gfx
     *      var ["source"] = Internal Source folder
     *      return void
     * -- MNU =Load common menu and tag gfx
     *      var ["effect"] = image effect request
     *      var ["source"] = source file
     *      var ["dest"] = dest file
     *      return void.
     *
     * @author Andrea Morello <lnxmcp@linhunix.com>
     *
     * @version GIT:2018-v1
     *
     * @param array $this->argIn temproraney array auto cleanable
     *
     * @return bool|array query results
     */
    public function moduleCore()
    {
        if (!isset($this->argIn['T'])) {
            return;
        }
        $this->getMcp()->Debug('GfxService Call:'.$this->argIn['T']);
        switch ($this->argIn['T']) {
            case 'H2T':
                if (!isset($this->argIn['html'])) {
                    return;
                }
                $html = $this->argIn['html'];
                $this->argOut = $this->getHtml2Txt($html);
                break;
            case 'INT':
                if (!isset($this->argIn['source'])) {
                    return;
                }
                $source = $this->argIn['source'];
                $mime = @$this->argIn['minetype'];
                $tag = @$this->argIn['tag'];
                $this->argOut = $this->callStaticCommon($source, $mime, $tag, $this->argIn);
                break;
             case 'DYN':
                if (!isset($this->argIn['source'])) {
                    return;
                }
                $source = $this->argIn['source'];
                $mime = @$this->argIn['minetype'];
                $tag = @$this->argIn['tag'];
                $this->argOut = $this->callDynamicCommon($source, $mime, $tag, $this->argIn);
                break;
            case 'MNU':
                if (!isset($this->argIn['source'])) {
                    return;
                }
                $source = $this->argIn['source'];
                $this->argOut = $this->loadMenusCommon($source);
                break;
            case 'DEF':
                if (!isset($this->argIn['source'])) {
                    return;
                }
                $defpack=$this->argIn['source'];
                $this->loadMenusCommon('Gfx/'.$defpack.'/mnu/default');
                $this->loadTagsCommon('Gfx/'.$defpack.'/tag/default');
               break;
            case 'IMG':
                if (!isset($this->argIn['effect'])) {
                    return;
                }
                if (!isset($this->argIn['source'])) {
                    return;
                }
                if (!isset($this->argIn['dest'])) {
                    return;
                }
                $effect = $this->argIn['effect'];
                $source = $this->argIn['source'];
                $dest = $this->argIn['dest'];
                $this->argOut = $this->getZebraImage($effect, $source, $dest, $this->argIn);
                break;
        }
    }
}
