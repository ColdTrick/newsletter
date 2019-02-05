<?php

if (PHP_SAPI !== 'cli') {
	exit('This script can only be run from the commandline');
}

$secret = '';
$entity_guid = 0;
$memory_limit = '64M';

foreach ($argv as $index => $arg) {
	if ($index < 1 || empty($arg)) {
		continue;
	}
	
	// arguments are passed key=value
	list($key, $value) = explode('=', $arg);
	
	switch ($key) {
		case 'host':
			$_SERVER['HTTP_HOST'] = $value;
			break;
		case 'https':
			$_SERVER['HTTPS'] = $value;
			break;
		case 'entity_guid':
			$value = (int) $value;

			if ($value > 0) {
				$$key = $value;
			}
			break;
		default:
			$$key = $value;
			break;
	}
}

if (empty($secret) || empty($entity_guid)) {
	exit('Wrong input to run this script, please provide a entity_guid and secret');
}

ini_set('memory_limit', $memory_limit);

// start the Elgg engine
$autoload_root = dirname(dirname(dirname(__DIR__)));
if (!is_file("$autoload_root/vendor/autoload.php")) {
	// installation through composer
	$autoload_root = dirname(dirname(__DIR__));
}

require_once "$autoload_root/vendor/autoload.php";

\Elgg\Application::start();

// vaildate the supplied secret
if (!newsletter_validate_commandline_secret($entity_guid, $secret)) {
	exit(elgg_echo('newsletter:cli:error:secret'));
}

// send the newsletter
newsletter_process($entity_guid);
