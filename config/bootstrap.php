<?php

use App\Bots\MyBot;
use danog\MadelineProto\API;

defined('APP_BASEPATH') || define('APP_BASEPATH', dirname(__DIR__));

require APP_BASEPATH . '/vendor/autoload.php';

$MadelineProto = new API(APP_BASEPATH . '/var/sessions/session.madeline');
$MadelineProto->async(true);
$MadelineProto->startAndLoop(MyBot::class);
