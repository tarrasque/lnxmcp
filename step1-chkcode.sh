#!/bin/bash
clear;
echo "LNX STEP 2 - CHECK CODE"
export MCP_HOME="$(dirname $0)";
let e=0;
find $MCP_HOME -type f  -name '*.php'| while read rr ; do
	ROW="$(    php -l "$rr" 2>&1 | grep -iv 'no syntax error' )";
	if [ "$ROW" == "" ]; then 
	    echo "check OK for $rr ";
	else
	    echo "check ERR found on $rr !!";
	    echo $ROW;
	    let e++;
	fi;
done;
echo "$e errors found !!!";
