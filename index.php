<?php

require_once(__DIR__.'/config.php');

//
// connect to odbc
//
$handle = false;
if ($_ENV['USE_PERSISTENT_ODBC']) {
    $handle = odbc_pconnect($_ENV['DSN'], $_ENV['DSN_USER'], $_ENV['DSN_PASSWORD']);
} else {
    $handle = odbc_connect($_ENV['DSN'], $_ENV['DSN_USER'], $_ENV['DSN_PASSWORD']);
}

if (!$handle) {
    http_response_code(500);
    die('Could not connect to ODBC DSN: '.$_ENV['DSN']);
}

debug('Connected to ODBC '.$_ENV['DSN']);

//
// read the JSON post body
//
$body = file_get_contents('php://input');
if (strlen($body) == 0) {
    http_response_code(405);
    die('POST was empty. Expected: POST of JSON data, SQL queries in an array.');
}

//
// decode the JSON
//
$json = @json_decode($body);
if ($json === false) {
    http_response_code(500);
    die('Could not decode JSON. Check your encoding.');
}

//
// make sure we've been given an array of SQL queries
//
if (!is_array($json)) {
    http_response_code(500);
    die('Expected JSON array of SQL queries.');
}
debug('Got '.count($json).' queries to execute');

//
// execute the queries, one by one
//
$response = array();
foreach ($json as $sql) {
    debug('Doing query: '.$sql);

    // we'll populate this array with our rows from ODBC
    $query_response_rows = array();

    $result = @odbc_exec($handle, $sql);
    if ($result) {
        while ($row = odbc_fetch_array($result)) {
            $query_response_rows[] = $row;
        }
        debug('Query returned '.count($query_response_rows).' rows');
    } else {
        http_response_code(500);
        $error = odbc_errormsg($result);
        die('Could not execute query: "'.$sql.'". The error (if any) was: '.$error);
    }

    // add an inner array to our overall response
    $response[] = $query_response_rows;

    // frees memory associated with the query
    if ($result) {
        odbc_free_result($result);
    }
}

header('Content-Type: application/json');
echo json_encode($response);

//
// disconnect from odbc
//
if ($handle && !$_ENV['USE_PERSISTENT_ODBC']) {
    odbc_close($handle);
}
