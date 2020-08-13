<?php

use App\Event\Event;

Event::register('MyBot.test', function ($update, $madeline) {
    /* var_dump($update, $madeline); */
    yield $madeline->messages->sendMessage([
        'peer' => $madeline::ADMIN_PEER,
        'message' => 'Hello world',
    ]);
});
