<?php
define("mysqlLegacyASSOC", MYSQLI_ASSOC);
function mysqlLegacyQuery($sql, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_query($conn, $sql);
}
function mysqlLegacyConnect($host, $user, $pass, $database = "information_schema", $port = "3306", $socket = null) {
    global $cn;
    if ($socket == null) {
        $cn = mysqli_connect($host, $user, $pass, $database, $port);
    } else {
        $cn = mysqli_connect($host, $user, $pass, $database, $port, $socket);
    }
    if ($cn == null) {
        $GLOBALS["cfg"]["lnxmcp"]->warning("Connect Error:".$host . "/" . $user . "/****/" . $database . "/" . $port . "/" . $socket);
    }else{
        $GLOBALS["cfg"]["lnxmcp"]->debug("Connect OK :".$host . "/" . $user . "/****/" . $database . "/" . $port . "/" . $socket);
    }
    return $cn;
}
/// PREFERRED DONT USE THIS
//function mysqlLegacyPconnect($host, $user, $pass, $client_flags = 0) {
//    global $cn;
//    $cn = mysqli_connect($host, $user, $password, null, null, null);
//    return $cn;
//}

function mysqlLegacySelectDb($database, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysqli_select_db($conn, $database);
}
function mysqlLegacyListTables($database, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_query($conn, 'SHOW TABLES FROM ' . $database);
}
function mysqlLegacyAffectedRows($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_affected_rows($conn);
}
function mysqlLegacyClientEncoding($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_character_set_name($conn);
}
function mysqlLegacyClose($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_close($conn);
}
function mysqlLegacyCreateDb($database, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_query($conn, 'CREATE DATABASE ' + $database);
}
function mysqlLegacyDataSeek($conn, $row_number) {
    return mysqli_data_seek($conn, $row_number);
}
function mysqlLegacyDbName($result, $row, mixed $fields = null) {
    mysqli_data_seek($result, $row);
    $fetch = mysqli_fetch_row($result);
    return $fetch[0];
}
function mysqlLegacyDbQuery($database, $sql, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    $r1 = mysqlLegacyQuery("SELECT database() ;");
    $r2 = mysqli_fetch_all($r1);
    $dbold = $r2["database()"];
    mysqli_select_db($database);
    $res = mysqli_query($conn, $sql);
    mysqli_select_db($dbold);
    return $res;
}
function mysqlLegacyDropDb($database, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_query($conn, 'DROP DATABASE ' + $database);
}
function mysqlLegacyErrNo($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_errno($conn);
}
function mysqlLegacyError($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_error($conn);
}
function mysqlLegacyEscapeString($unescaped_string) {
    global $cn;
    return mysqli_escape_string($cn, $unescaped_string);
}
function mysqlLegacyFetchArray($result, $result_type = MYSQL_BOTH) {
    return mysqli_fetch_array($result, $result_type);
}
function mysqlLegacyFetchAssoc($result) {
    return mysqli_fetch_assoc($result);
}
function mysqlLegacyFetchField($result, $field_offset = 0) {
    if ($field_offset != 0) {
        for ($x = 0; $x < $field_offset; $x++) {
            mysqli_fetch_field($result);
        }
    }
    return mysqli_fetch_field($result);
}
function mysqlLegacyFetchLengths($result) {
    return mysqli_fetch_lengths($result);
}
function mysqlLegacyFetchObject($result, $class_name, array $params) {
    return mysqli_fetch_object($result, $class_name, $params);
}
function mysqlLegacyFetchRow($result) {
    return mysqli_fetch_row($result);
}
function mysqlLegacyFieldFlags($result, $field_offset) {
    $flags = array();
    $constants = get_defined_constants(true);
    foreach ($constants['mysqli'] as $c => $n) {
        if (preg_match('/MYSQLI_(.*)_FLAG$/', $c, $m))
            if (!array_key_exists($n, $flags))
                $flags[$n] = $m[1];
        $flags_num = mysqli_fetch_field_direct($result, $field_offset)->flags;
        $result = array();
        foreach ($flags as $n => $t)
            if ($flags_num & $n)
                $result[] = $t;
        $returnFlags = implode(' ', $result);
        $returnFlags = str_replace('PRI_KEY', 'PRIMARY_KEY', $returnFlags);
        $returnFlags = strtolower($returnFlags);
    }
    return $returnFlags;
}
function mysqlLegacyFieldLen($result, $field_offset) {
    $fieldInfo = mysqli_fetch_field_direct($result, $field_offset);
    return $fieldInfo->length;
}
function mysqlLegacyFieldName($result, $field_offset) {
    $fieldInfo = mysqli_fetch_field_direct($result, $field_offset);
    return $fieldInfo->name;
}
function mysqlLegacyFieldSeek($result, $field_offset) {
    return mysqli_field_seek($result, $field_offset);
}
function mysqlLegacyFieldTable($result, $field_offset) {
    $fieldInfo = mysqli_fetch_field_direct($result, $field_offset);
    return $fieldInfo->table;
}
function mysqlLegacyFieldType($result, $field_offset) {
    $type_id = mysqli_fetch_field_direct($result, $field_offset)->type;
    $types = array();
    $constants = get_defined_constants(true);
    foreach ($constants['mysqli'] as $c => $n)
        if (preg_match('/^MYSQLI_TYPE_(.*)/', $c, $m))
            $types[$n] = $m[1];
    $resultType = array_key_exists($type_id, $types) ? $types[$type_id] : NULL;
    return $resultType;
}
function mysqlLegacyFreeResult($result) {
    return mysqli_free_result($result);
}
function mysqlLegacyGetClientInfo($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_get_client_info($conn);
}
function mysqlLegacyGetHostInfo($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_get_host_info($conn);
}
function mysqlLegacyGetProtoInfo($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_get_proto_info($conn);
}
function mysqlLegacyGetServerInfo($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_get_server_info($conn);
}
function mysqlLegacyInfo($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_info($conn);
}
function mysqlLegacyInsertId($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_insert_id($conn);
}
function mysqlLegacyListDbs($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_query($link, 'SHOW DATABASES');
}
function mysqlLegacyListFields($database, $table_name, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqlLegacyDbQuery($database, 'SHOW COLUMNS FROM ' + $table_name, $conn);
}
function mysqlLegacyListProcesses($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_thread_id($conn);
}
function mysqlLegacyNumFields($result) {
    return mysqli_field_count($result);
}
function mysqlLegacyNumRows($result) {
    return mysqli_num_rows($result);
}
function mysqlLegacyPing($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_ping($conn);
}
function mysqlLegacyRealEscapeString($unescaped_string, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_real_escape_string($unescaped_string, $conn);
}
function mysqlLegacyResult($result, $row, mixed $fields = null) {
    mysqli_data_seek($result, $row);
    if (!empty($field)) {
        while ($finfo = mysqli_fetch_field($result)) {
            if ($field == $finfo->name) {
                $f = mysqli_fetch_assoc($result);
                $fetch = $f[$field];
            }
        }
    } else {
        $f = mysqli_fetch_array($result);
        $fetch = $f[0];
    }
    return $fetch;
}
function mysqlLegacySetCharset($charset, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_set_charset($conn, $charset);
}
function mysqlLegacyStat($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_stat($conn);
}
function mysqlLegacyTableName($result, $i) {
    mysqli_data_seek($result, $i);
    $f = mysqli_fetch_array($result);
    return $f[0];
}
function mysqlLegacyThreadId($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_thread_id($conn);
}
function mysqlLegacyUnbufferedQuery($sql, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return mysqli_query($conn, $sql, MYSQLI_USE_RESULT);
}
