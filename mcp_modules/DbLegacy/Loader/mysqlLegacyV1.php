<?php
define("mysqlLegacyASSOC", MYSQL_ASSOC);

function mysqlLegacyQuery($sql, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
//return mysqli_query($conn, $sql);
    return @mysql_query($sql, $conn);
}

function mysqlLegacyConnect($host, $user, $pass, $database = null, $port = null, $socket = null) {
    global $cn;
    $cn = @mysql_connect($host, $user, $pass, $database, $port);
    return $cn;
}

/// PREFERRED DONT USE THIS
//function mysqlLegacyPconnect($host, $user, $pass, $client_flags = 0) {
//    global $cn;
//    $cn = @mysql_pconnect($host, $user, $password, $client_flags);
////$cn = mysqli_connect($host, $user, $password, $database, $port, $socket);
//    return $cn;
//}

function mysqlLegacySelectDb($database, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_select_db($database, $conn);
//return mysqli_select_db($conn, $database);
}

function mysqlLegacyListTables($database, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
//return mysqli_query($conn, 'SHOW TABLES FROM ' . $database);
    return @mysql_list_tables($database, $conn);
}

function mysqlLegacyAffectedRows($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_affected_rows($conn);
}

function mysqlLegacyClientEncoding($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_client_encoding($conn);
}

function mysqlLegacyClose($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_close($conn);
}

function mysqlLegacyCreateDb($database, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_create_db($database, $conn);
}

function mysqlLegacyDataSeek($conn, $row_number) {
    return @mysql_data_seek($conn, $row_number);
}

function mysqlLegacyDbName($result, $row, mixed $fields = null) {
    return @mysql_db_name($result , $row, $fields);
}

function mysqlLegacyDbQuery($database, $sql, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_db_query($database, $sql, $conn);
}

function mysqlLegacyDropDb($database, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_drop_db($database, $conn);
}

function mysqlLegacyErrNo($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_errno($conn);
}

function mysqlLegacyError($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_error($conn);
}

function mysqlLegacyEscapeString($unescaped_string) {
    return @mysql_escape_string($unescaped_string);
}

function mysqlLegacyFetchArray($result, $result_type = MYSQL_BOTH) {
    return @mysql_fetch_array($result, $result_type);
}

function mysqlLegacyFetchAssoc($result) {
    return @mysql_fetch_assoc($result);
}

function mysqlLegacyFetchField($result, $field_offset = 0) {
    return @mysql_fetch_field($result, $field_offset);
}

function mysqlLegacyFetchLengths($result) {
    return @mysql_fetch_lengths($result);
}

function mysqlLegacyFetchObject($result, $class_name, array $params) {
    return @mysql_fetch_object($result, $class_name, $params);
}

function mysqlLegacyFetchRow($result) {
    return @mysql_fetch_row($result);
}

function mysqlLegacyFieldFlags($result, $field_offset) {
    return @mysql_field_flags($result, $field_offset);
}

function mysqlLegacyFieldLen($result, $field_offset) {
    return @mysql_field_len($result, $field_offset);
}

function mysqlLegacyFieldName($result, $field_offset) {
    return @mysql_field_name($result, $field_offset);
}

function mysqlLegacyFieldSeek($result, $field_offset) {
    return @mysql_field_seek($result, $field_offset);
}

function mysqlLegacyFieldTable($result, $field_offset) {
    return @mysql_field_table($result, $field_offset);
}

function mysqlLegacyFieldType($result, $field_offset) {
    return @mysql_field_type($result, $field_offset);
}

function mysqlLegacyFreeResult($result) {
    return @mysql_free_result($result);
}

function mysqlLegacyGetClientInfo($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_get_client_info();
}

function mysqlLegacyGetHostInfo($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_get_host_info($conn);
}

function mysqlLegacyGetProtoInfo($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_get_proto_info($conn);
}

function mysqlLegacyGetServerInfo($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_get_server_info($conn);
}

function mysqlLegacyInfo($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_info($conn);
}

function mysqlLegacyInsertId($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_insert_id($conn);
}

function mysqlLegacyListDbs($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_list_dbs($conn);
}

function mysqlLegacyListFields($database, $table_name, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_list_fields($database, $table_name, $conn);
}

function mysqlLegacyListProcesses($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_list_processes($conn);
}

function mysqlLegacyNumFields($result) {
    return @mysql_num_fields($result);
}

function mysqlLegacyNumRows($result) {
    return @mysql_num_rows($result);
}

function mysqlLegacyPing($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_ping($conn);
}

function mysqlLegacyRealEscapeString($unescaped_string, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_real_escape_string($unescaped_string, $conn);
}

function mysqlLegacyResult($result, $row, mixed $fields = null) {
    return @mysql_result($result, $row, $fields);
}

function mysqlLegacySetCharset($charset, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_set_charset($charset, $conn);
}

function mysqlLegacyStat($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_stat($conn);
}

function mysqlLegacyTableName($result, $i) {
    return @mysql_tablename($result, $i);
}

function mysqlLegacyThreadId($conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_thread_id($conn);
}

function mysqlLegacyUnbufferedQuery($sql, $conn = null) {
    global $cn;
    if ($conn == null) {
        $conn = $cn;
    }
    return @mysql_unbuffered_query($sql, $conn);
}
