<?php
/**
 * Created by PhpStorm.
 * User: linhunix
 * Date: 9/4/2018
 * Time: 10:11 AM
 */
namespace LinHUniX\Mcp\Model;
use LinHUniX\Mcp\masterControlProgram;

interface mcpServiceProviderModelClass
{
    public function register (masterControlProgram &$mcp, mcpConfigArrayModelClass &$cfg);
}