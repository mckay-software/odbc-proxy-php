<?php

require_once '../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->overload();
$dotenv->required('SCADAFARM_ENVIRONMENT')->allowedValues([
	'DEVELOPMENT',
	'PRODUCTION'
]);
$dotenv->required(['ENVIRONMENT']);
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
