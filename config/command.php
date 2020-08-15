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

Event::register('MyBot.say', function ($update, $madeline) {
    $params = CommandParser::parseString($update['message']['message']);
    if (isset($params['say']['text'])) {
        $message = CommandParser::parseMacro($params['say']['text']);
        CommandParser::refreshMacro();

        yield $madeline->messages->editMessage([
            'peer' => $update,
            'id' => $update['message']['id'],
            'message' => $message,
        ]);
    }
});
Event::register('MyBot.logger', function ($update) {
    yield Logger::log($update, Logger::VERBOSE);
});
Event::register('MyBot.action.playing', function ($update, $madeline) {
    $madeline->messages->setTyping([
        'peer' => $update,
        'action' => [
            '_' => 'sendMessageGamePlayAction',
        ]
    ]);
});
