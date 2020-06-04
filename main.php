<?php

require './vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Logger;
use danog\MadelineProto\Tools;
use danog\MadelineProto\RPCErrorException;
use danog\MadelineProto\Exception;
use Wheeler\Fortune\Fortune;
use Cvar1984\Madeline\Command;

class Mybot extends Command
{
    public function onAny($update): \Generator
    {
        yield Logger::log($update, Logger::VERBOSE);
    }
    public function onUpdateDeleteMessages(array $update): \Generator
    {
        yield $this->onUpdateDeleteChannelMessages($update);
    }
    public function onUpdateDeleteChannelMessages(array $update): \Generator
    {
        try {
            // i have recorded that
            yield $this->messages->setTyping([
                'peer' => $update,
                'action' => [
                    '_' => 'sendMessageRecordAudioAction',
                ],
            ]);
        } catch (Exception | RPCErrorException $e) {
            //$this->report($e);
        }
    }
    public function onUpdateNewMessage(array $update): \Generator
    {
        try {
            // i had a boss fight on game
            yield $this->messages->setTyping([
                'peer' => $update,
                'action' => [
                    '_' => 'sendMessageGamePlayAction',
                ],
            ]);
        } catch (Exception | RPCErrorException $e) {
            //$this->report($e);
        }

        yield $this->onUpdateNewChannelMessage($update);
    }
    public function onUpdateNewChannelMessage(array $update): \Generator
    {
        if (!isset($update['message']['message'])) {
            return;
        }
        if (!isset($update['message']['from_id'])) {
            return;
        }
        if (!isset($update['message']['id'])) {
            return;
        }

        $message = $update['message']['message'];
        $fromId = $update['message']['from_id'];
        $chatId = $update['message']['id'];
        $peer = $update;

        if (preg_match('/^\/urban/i', $message)) {
            try {
                if (!preg_match('/\s"(.+)"/i', $message, $match)) {
                    throw new Exception('query is empty');
                }

                $query = $match[1];

                yield $this->urban([
                    'message' => $query,
                    'peer' => $peer,
                    'id' => $chatId,
                ]);
            } catch (Exception $e) {
                yield $this->messages->sendMessage([
                    'peer' => $peer,
                    'reply_to_msg_id' => $chatId,
                    'message' => $e->getMessage(),
                ]);
                // yield $thiz->report($e);
            }
        } elseif (preg_match('/^\/lite/i', $message)) {
            try {
                if (!preg_match('/\s"(.+\d)"/', $message, $match)) {
                    throw new Exception('Not a valid phone number');
                }

                $numberPhone = $match[1];

                yield $this->liteOtp([
                    'peer' => $peer,
                    'id' => $chatId,
                    'message' => $numberPhone
                ]);
            } catch (Exception $e) {
                yield $this->messages->sendMessage([
                    'peer' => $peer,
                    'reply_to_msg_id' => $chatId,
                    'message' => $e->getMessage()
                ]);
            }
        } elseif (preg_match('/^\/checker/i', $message)) {
            try {
                if (!preg_match('/\s"(.*\d)"/', $message, $match)) {
                    throw new Exception('Not a valid cc');
                }

                $text = $match[1];

                yield $this->isValidCreditCard([
                    'peer' => $peer,
                    'id' => $chatId,
                    'message' => $text,
                ]);
            } catch (Exception $e) {
                yield $this->messages->sendMessage([
                    'peer' => $peer,
                    'reply_to_msg_id' => $chatId,
                    'message' => $e->getMessage(),
                ]);
            }
        } elseif (preg_match('/^\/simpleimage/i', $message)) {
            preg_match('/\s"(.+)"/Usi', $message, $match)
                ? ($text = $match[1])
                : ($text = Fortune::make());
            yield $this->simpleImage([
                'peer' => $peer,
                'id' => $chatId,
                'message' => $text,
                'quality' => 100,
                'query' => 'Dark',
            ]);
        } elseif (preg_match('/^\/fortune/i', $message)) {
            yield $this->fortune([
                'peer' => $peer,
                'id' => $chatId,
            ]);
        } elseif ($fromId == $this::ADMIN_ID) {
            // admin commmand
            if (preg_match('/^\/animate/', $message)) {
                preg_match('/\s"(.+)"/Usi', $message, $match)
                    ? ($text = $match[1])
                    : ($text = Fortune::make());
                yield $this->animate([
                    'peer' => $peer,
                    'message' => $text,
                    'id' => $chatId,
                ]);
            } elseif (preg_match('/^\/loop/i', $message)) {
                try {
                    preg_match('/"\s(.*\d)/', $message, $match)
                        ? ($count = $match[1])
                        : ($count = 1);
                    preg_match('/^\/loop\s"(.*)"/i', $message, $match)
                        ? ($text = $match[1])
                        : ($text = false);
                    yield $this->loopCommand([
                        'peer' => $peer,
                        'id' => $chatId,
                        'message' => $text,
                        'count' => $count,
                    ]);
                } catch (Exception $e) {
                    yield $this->messages->editMessage([
                        'peer' => $peer,
                        'id' => $chatId,
                        'message' => $e->getMessage(),
                    ]);
                    // yield $this->report($e);
                }
            } elseif (preg_match('/^\/upfile/i', $message)) {
                try {
                    preg_match('/\s"(.*)"/Usi', $message, $match)
                        ? ($file = $match[1])
                        : ($file = $this::STORAGE . '/default.jpg');

                    yield $this->upfile([
                        'peer' => $peer,
                        'id' => $chatId,
                        'file' => $file,
                    ]);
                } catch (Exception | RPCErrorException $e) {
                    yield $this->messages->editMessage([
                        'peer' => $peer,
                        'id' => $chatId,
                        'message' => $e->getMessage(),
                    ]);
                    //yield $this->report($e);
                }
            } elseif (preg_match('/^\/channel/i', $message)) {
                try {
                    preg_match('/\s"(.+)"/Usi', $message, $match)
                        ? ($text = $match[1])
                        : ($text = Fortune::make());

                    $speed = (yield $this->channelCommand([
                        'peer' => $this::CHANNEL_PEER,
                        'id' => $chatId,
                        'message' => $text,
                        'quality' => 100,
                        'query' => 'Dark',
                    ]));
                    $text = 'Uploaded in *' . $speed . 'ms*';
                } catch (RPCErrorException $e) {
                    $text = $e->getMessage();
                }
                yield $this->messages->editMessage([
                    'peer' => $peer,
                    'id' => $chatId,
                    'parse_mode' => 'Markdown',
                    'message' => $text,
                ]);
            }
        }
        yield $this->onUpdateEditMessage($update);
    }
    public function onUpdateEditChannelMessage(array $update): \Generator
    {
        yield $this->onUpdateEditMessage($update);
    }
    public function onUpdateEditMessage($update): \Generator
    {
        if (!isset($update['message']['message'])) {
            return;
        }
        if (!isset($update['message']['from_id'])) {
            return;
        }
        if (!isset($update['message']['id'])) {
            return;
        }
        if (!$update['message']['from_id'] == $this::ADMIN_ID) {
            return;
        }

        $fromId = $update['message']['from_id'];
        $message = $update['message']['message'];
        $chatId = $update['message']['id'];
        $peer = $update;

        if (preg_match('/^\/eval/i', $message)) {
            try {
                $text = substr($message, 6);
                yield $this->evalCommand([
                    'peer' => $peer,
                    'id' => $chatId,
                    'message' => $text,
                ]);
            } catch (Exception | RPCErrorException | ParseError $e) {
                $text = $e->getMessage();
                yield $this->messages->editMessage([
                    'peer' => $peer,
                    'id' => $chatId,
                    'message' => $text,
                ]);
                //yield $this->report($e);
            }
        } elseif (preg_match('/^\/\//i', $message)) {
            try {
                $text = substr($message, 2);
                yield $this->systemCommand([
                    'peer' => $peer,
                    'id' => $chatId,
                    'message' => $text
                ]);
            } catch (Exception | RPCErrorException $e) {
                $text = $e->getMessage();
                yield $this->messages->editMessage([
                    'peer' => $peer,
                    'id' => $chatId,
                    'message' => $text
                ]);
            }
        }
    }
    public function getReportPeers()
    {
        return [self::ADMIN_PEER];
    }
}

$settings = [
    'logger' => [
        'param' => MyBot::STORAGE . '/Madeline.log',
    ],
    'max_tries' => [
        'query' => 1,
    ],
];

$MadelineProto = new API('session.madeline', $settings);
$MadelineProto->async(true); // async only work for builtin madeline method
$MadelineProto->startAndLoop(MyBot::class);
