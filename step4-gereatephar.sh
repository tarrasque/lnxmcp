#!/bin/bash
clear;
echo "LNX STEP 4 - GENERATE PHAR FILE "
export MCP_HOME="$(dirname $0)";
$MCP_HOME/mcp_extras/pharize.sh ;