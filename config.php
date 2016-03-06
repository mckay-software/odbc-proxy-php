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
