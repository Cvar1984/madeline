<?php

use App\Event\Event;
use samejack\PHP\ArgvParser;
use danog\MadelineProto\Logger;

$parser = new ArgvParser();

Event::register('MyBot.test', function ($update, $madeline) use ($parser) {
    $params = $parser->parseConfigs($update['message']['message']);
    var_dump($params);
    /* yield $madeline->messages->sendMessage([ */
    /*     'peer' => $madeline::ADMIN_PEER, */
    /*     'message' => 'Hello world', */
    /* ]); */
});
Event::register('MyBot.logger', function ($update) {
    yield Logger::log($update, Logger::VERBOSE);
});
