<?php

use App\Event\Event;
use App\Tools\{CommandParser, MacroParser};
use danog\MadelineProto\Logger;

/* $chatId = $update['message']['id']; */
/* $message = $update['message']['message']; */
/* $fromId = $update['message']['from_id']; */
/* $peer = $update */

Event::register('MyBot.say', function ($update, $madeline) {
    $params = CommandParser::parseString($update['message']['message']);
    var_dump($params);
    if (isset($params['say']['text'])) {
        $message = MacroParser::parseString($params['say']['text']);
        MacroParser::refreshMacro();

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
