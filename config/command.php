<?php

use App\Event\Event;
use App\Tools\CommandParser;
use danog\MadelineProto\Logger;

/* $chatId = $update['message']['id']; */
/* $message = $update['message']['message']; */
/* $fromId = $update['message']['from_id']; */
/* $peer = $update['message']['message']; */
function array_keys_exists(array $keys, $array)
{
    return is_array($array)
        ? !array_diff_key(array_flip($keys), $array)
        : $array;
}

Event::register('MyBot.test', function ($update, $madeline) {
    $params = CommandParser::parseString($update['message']['message']);
    /* var_dump($params); */
    if (array_keys_exists(['test'], $params)) {
        yield $madeline->messages->editMessage([
            'peer' => $update,
            'id' => $update['message']['id'],
            'message' => json_encode($params, JSON_PRETTY_PRINT),
        ]);
    }
});
Event::register('MyBot.logger', function ($update) {
    yield Logger::log($update, Logger::VERBOSE);
});
Event::register('MyBot.macro', function ($update) {
    return preg_replace($update['message']['message']);
});
