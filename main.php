<?php

require './vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Logger;
use danog\MadelineProto\Tools;
use danog\MadelineProto\RPCErrorException;
use danog\MadelineProto\Exception;
use Wheeler\Fortune\Fortune;
use Cvar1984\Madeline\Layer\Command;

/**
 * List of exception types
 \danog\MadelineProto\Exception
 - Default exception, thrown when a php error occures and in a lot of other cases

 \danog\MadelineProto\RPCErrorException
 - Thrown when an RPC error occurres (an error received via the MTProto API): note that the error message of this exception is localized in English, and may vary: to fetch the original API error message use $e->rpc.

 \danog\MadelineProto\TL\Exception
 - Thrown on TL serialization/deserialization errors

 \danog\MadelineProto\ResponseException
 - Thrown when an unexpected message is received through the socket

 \danog\MadelineProto\NothingInTheSocketException
 - Thrown if no data can be read/written on the TCP socket

 \danog\MadelineProto\PTSException
 - Thrown if the PTS is unrecoverably corrupted

 \danog\MadelineProto\SecurityException
 - Thrown on security problems (invalid params during generation of auth key or similar)

 \danog\MadelineProto\TL\Conversion\Exception
 - Thrown if some param/object can’t be converted to/from bot API/TD/TD-CLI format (this includes markdown/html parsing)
 */
class Mybot extends Command
{
    public function onAny($update)
    {
        Logger::log($update);
    }
    public function onupdateDeleteChannelMessages(array $update): \Generator
    {
        return $this->onUpdateDeleteMessages($update);
    }
    public function onUpdateDeleteMessages(array $update): \Generator
    {
        yield $this->messages->sendMessage([
            'peer' => $update,
            'message' => 'hmm..',
        ]);
    }
    public function onUpdateNewChannelMessage(array $update): \Generator
    {
        return $this->onUpdateNewMessage($update);
    }
    public function onUpdateNewMessage(array $update): \Generator
    {
        if (empty($update['message']['message'])) {
            return;
        }
        $message = $update['message']['message'];
        $chatId = $update['message']['id'];
        $fromId = @$update['message']['from_id'];
        $peer = $update;

        if (preg_match('/^\/urban/i', $message)) {
            try {
                if (preg_match('/\s"(.+)"/i', $message, $match)) {
                    $query = $match[1];
                } else {
                    throw new Exception('query is empty');
                }
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
        } elseif (preg_match('/^\/simpleimage/i', $message)) {
            preg_match('/\s"(.+)"/i', $message, $match)
                ? ($text = $match[1])
                : ($text = Fortune::make());
            yield $this->simpleImage([
                'peer' => $peer,
                'id' => $chatId,
                'message' => $text,
            ]);
        } elseif (preg_match('/^\/fortune/i', $message)) {
            yield $this->fortune([
                'peer' => $peer,
                'id' => $chatId,
            ]);
        } elseif (@$fromId == $this::ADMIN_ID) {
            // admin commmand
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
            } elseif (preg_match('/^\/animate/', $message)) {
                preg_match('/\s"(.+)"/i', $message, $match)
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
                    preg_match('/\s"(.+)"/', $message, $match)
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
                    preg_match('/\s"(.+)"/i', $message, $match)
                        ? ($text = $match[1])
                        : ($text = Fortune::make());
                    $speed = (yield $this->channelCommand([
                        'peer' => $this::CHANNEL_PEER,
                        'message' => $text,
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
        return $this->onUpdateEditChannelMessage($update);
    }
    public function onUpdateEditChannelMessage(array $update): \Generator
    {
        return $this->onUpdateEditMessage($update);
    }
    public function onUpdateEditMessage($update): \Generator
    {
        if (empty($update['message']['message'])) {
            return;
        }
        $message = $update['message']['message'];
        $chatId = $update['message']['id'];
        $fromId = @$update['message']['from_id'];
        $peer = $update;

        if ($fromId != $this::ADMIN_ID) {
            return;
        }
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
