<?php

declare(strict_types=1);

namespace svoboda\negotiator;


// Files of the project
use svoboda\negotiator\models\telegram;

// Framework for PHP
use mirzaev\minimal\core;

// Framework for Telegram
use Zanzara\Zanzara,
	Zanzara\Context,
	Zanzara\Config;

// Enabling debugging
/* ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1); */

// Initializing path to the public directory 
define('INDEX', __DIR__);

// Initializing path to the project root directory
define('ROOT',  INDEX . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR	. '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

// Initializing path to the directory of settings 
define('SETTINGS', INDEX . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'settings');

// Initializing path to the storage
define('STORAGE', INDEX . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'storage');

// Initiailizing telegram key
define('TELEGRAM_KEY', require(SETTINGS . DIRECTORY_SEPARATOR . 'telegram.php'));

// Initializing dependencies
require ROOT . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

var_dump(TELEGRAM_KEY);

// Initializing core
$core = new core(namespace: __NAMESPACE__);

$config = new Config();
$config->setParseMode(Config::PARSE_MODE_MARKDOWN);
$config->useReactFileSystem(true);

$bot = new Zanzara(TELEGRAM_KEY, $config);

$bot->onUpdate(function (Context $ctx): void {
	var_dump('biba');
});

/* $bot->onCommand('start', fn($ctx) => telegram::start($ctx)); */
$bot->onCommand('society', fn($ctx) => telegram::society($ctx));

// Starting chat-robot
$bot->run();
