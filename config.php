<?php

require_once __DIR__.'/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->overload();
$dotenv->required('ENVIRONMENT')->allowedValues([
	'DEVELOPMENT',
	'PRODUCTION'
]);
$dotenv->required(['ENVIRONMENT']);
$dotenv->required(['DSN_USER']);
$dotenv->required(['DSN_PASSWORD']);
$dotenv->required(['USE_PERSISTENT_ODBC']);
$dotenv->load();

if (!empty($_ENV['ROLLBAR_ACCESS_TOKEN'])) {
	if (class_exists('Rollbar')) {
		Rollbar::init(array(
			'access_token' => $_ENV['ROLLBAR_ACCESS_TOKEN'],
			'environment' => $_ENV['ENVIRONMENT']
		));
	} else {
		error_log('Rollbar App ID found in .env, but Rollbar not installed.');
	}
}

//
// debug message helper
//
function debug($message) {
    if ($_ENV['ENVIRONMENT'] === 'DEVELOPMENT') {
        error_log($message);
    }
}

//
// forces UTF-8 encoding
//
function force_encoding($array) {
	if ($array && is_array($array)) {
		// There's some UTF-8 issue in PHP 5.5 that we didn't see in PHP 5.3,
		// the solution is to iterate over the array and make sure that
		// everything is encoded as UTF-8
		array_walk_recursive($array, function(&$item, $key){
	        if (!mb_detect_encoding($item, 'utf-8', true)) {
	        	$item = utf8_encode($item);
	        }
	    });
	}
	return $array;
}
