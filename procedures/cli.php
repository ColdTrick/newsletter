<?php

if (PHP_SAPI === "cli") {
	$secret = "";
	$entity_guid = 0;
	$memory_limit = "64M";
	
	foreach ($argv as $index => $arg) {
		if (($index > 0) && !empty($arg)) {

			list($key, $value) = explode("=", $arg);

			switch ($key) {
				case "host":
					$_SERVER["HTTP_HOST"] = $value;
					break;
				case "https":
					$_SERVER["HTTPS"] = $value;
					break;
				case "entity_guid":
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
	}

	if (!empty($secret) && !empty($entity_guid)) {
		ini_set("memory_limit", $memory_limit);
		
		// start the Elgg engine
		require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
		
		// vaildate the supplied secret
		if (newsletter_validate_commandline_secret($entity_guid, $secret)) {
			// send the newsletter
			newsletter_process($entity_guid);
		} else {
			exit(elgg_echo("newsletter:cli:error:secret"));
		}
	} else {
		exit("Wrong input to run this script, please provide a entity_guid and secret");
	}
} else {
	exit("This script can only be run from the commandline");
}