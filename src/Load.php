<?php

/**
 * Created by PhpStorm.
 * User: LinHUniX Andrea Morello
 * Date: 9/4/2018
 * Time: 9:22 PM
 */
class mcpAutoload
{
    public $results;
    private $count=0;

    public function __construct ($dir="")
    {
        if ($dir==""){
            $dir=__DIR__;
        }
        $this->results=array();
        $this->getDirContents ($dir);
        $this->load ();
    }
    function setres($resval){
        $this->results[$this->count]=$resval;
        $this->count++;
    }
    private function getDirContents ($dir)
    {
        $files = scandir ($dir);
        foreach ($files as $key => $value) {
            $path = realpath ($dir . DIRECTORY_SEPARATOR . $value);
            if (is_dir ($path)==false) {
                $this->setres($path);
            } else if ($value != "." && $value != "..") {
                $this->getDirContents ($path);
            }
        }
    }

    private function load ()
    {
        foreach ($this->results as $file) {
            $file_parts = pathinfo ($file);
            if ($file_parts['extension'] = "php") {
                include_once $file;
            }
        }
    }
}

/// AUTO RUN
$mcp_autoload=new mcpAutoload($mcp_path);