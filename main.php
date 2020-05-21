<?php

require './vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Logger;
use danog\MadelineProto\Tools;
use danog\MadelineProto\RPCErrorException;
use danog\MadelineProto\Exception;
use danog\MadelineProto\FileCallback;
use Cvar1984\Api\RapidApi;
use Bhsec\SimpleImage\Gambar;
use Wheeler\Fortune\Fortune;
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
class Mybot extends EventHandler
{
    public const ADMIN_ID = '905361440';
    public const ADMIN_PEER = 'Cvar1984';
    public const STORAGE = './assets';

    public function urban($opt)
    {
        $text = $opt['message'];
        $peer = $opt['peer'];
        $chatId = $opt['id'];
        $data = RapidApi::urban($text);
        $data = $data->list[0];
        $text = <<<MD
**definition:** *{$data->definition}*
**permalink:** `{$data->permalink}`
MD;
        yield $this->messages->sendMessage([
            'peer' => $peer,
            'reply_to_msg_id' => $chatId,
            'parse_mode' => 'Markdown',
            'message' => $text,
        ]);
    }
    public function simpleImage($opt)
    {
        $text = $opt['message'];
        $peer = $opt['peer'];
        $chatId = $opt['id'];
        $images = new Gambar($text, 'dark');
        $text = $images->getResult(
            $this::STORAGE . '/result.jpg',
            'image/jpeg',
            100
        );

        yield $this->messages->sendMedia([
            'peer' => $peer,
            'reply_to_msg_id' => $chatId,
            'parse_mode' => 'Markdown',
            'media' => [
                '_' => 'inputMediaUploadedPhoto',
                'file' => new FileCallback(
                    $this::STORAGE . '/result.jpg',
                    function ($progress) use ($peer, $chatId) {
                        yield $this->messages->editMessage([
                            'peer' => $peer,
                            'id' => $chatId,
                            'message' => 'Upload progress: ' . $progress . '%',
                        ]);
                        usleep(300000);
                    }
                ),
            ],
            'message' => '`' . $text . '`',
        ]);
    }
    public function fortune($opt)
    {
        $peer = $opt['peer'];
        $chatId = $opt['id'];
        $text = Fortune::make();

        yield $this->messages->sendMessage([
            'peer' => $peer,
            'reply_to_msg_id' => $chatId,
            'message' => '`' . $text . '`',
            'parse_mode' => 'Markdown',
        ]);
    }
    public function animate($opt)
    {
        $text = $opt['message'];
        $peer = $opt['peer'];
        $chatId = $opt['id'];
        $temposleep = 300000;
        $oldtext = '';
        $num = 1;

        yield $this->messages->editMessage([
            'no_webpage' => true,
            'peer' => $peer,
            'id' => $chatId,
            'message' => "|",
            'entities' => [
                [
                    '_' => 'messageEntityCode',
                    'offset' => 0,
                    'length' => 1,
                ],
            ],
        ]);
        usleep($temposleep);
        while ($oldtext != $text) {
            $oldtext = substr($text, 0, $num);
            $oldtext1 = $oldtext . "|";
            yield $this->messages->editMessage([
                'no_webpage' => true,
                'peer' => $peer,
                'id' => $chatId,
                'message' => $oldtext1,
                'parse_mode' => 'HTML',
                'entities' => [
                    [
                        '_' => 'messageEntityCode',
                        'offset' => 0,
                        'length' => strlen($oldtext1),
                    ],
                ],
            ]);
            usleep($temposleep);
            yield $this->messages->editMessage([
                'no_webpage' => true,
                'peer' => $peer,
                'id' => $chatId,
                'message' => $oldtext,
                'parse_mode' => 'HTML',
                'entities' => [
                    [
                        '_' => 'messageEntityCode',
                        'offset' => 0,
                        'length' => strlen($oldtext),
                    ],
                ],
            ]);
            $num++;
            usleep($temposleep);
        }
    }
    public function loopCommand($opt)
    {
        /**
         * WARNING, when you use loop to loop other command
         * you will send output to any event because
         * given peer and chatId is using dynamic properties
         * */
        $start = microtime(true) * 1000;
        $text = $opt['message'];
        $peer = $opt['peer'];
        $chatId = $opt['id'];
        $count = $opt['count'];

        for ($x = 0; $x < $count; $x++) {
            if ($text === false) {
                $buff = $text = Fortune::make();
            }

            $this->messages->sendMessage([
                'peer' => $peer,
                'message' => $text,
            ]);

            if (@$buff == $text) {
                $text = false;
            }
        }
        $speed = round(microtime(true) * 1000 - $start);
        $this->messages->editMessage([
            'peer' => $peer,
            'id' => $chatId,
            'parse_mode' => 'Markdown',
            'message' => $speed . ' *ms*',
        ]);
    }
    public function upfile($opt)
    {
        $file = $opt['file'];
        $peer = $opt['peer'];
        $chatId = $opt['id'];

        yield $this->messages->sendMedia([
            'peer' => $peer,
            'parse_mode' => 'Markdown',
            'media' => [
                '_' => 'inputMediaUploadedDocument',
                'file' => new FileCallback($file,
                function ($progress) use($peer, $chatId) {
                    yield $this->messages->editMessage([
                        'peer' => $peer,
                        'id' => $chatId,
                        'message' => 'Upload progress: ' . $progress . '%'
                    ]);
                    usleep(300000);
                }),
            ],
            'message' => '*' . basename($file) . '* has been uploaded',
        ]);
    }
    public function evalCommand($opt)
    {
        $text = $opt['message'];
        $peer = $opt['peer'];
        $chatId = $opt['id'];

        ob_start();
        eval($text);
        $text = ob_get_clean();

        yield $this->messages->editMessage([
            'peer' => $peer,
            'message' => $text,
            'id' => $chatId,
        ]);
    }
    public function channelCommand($opt)
    {
        $text = $opt['message'];
        $peer = $opt['peer'];

        $start = microtime(true) * 1000;
        $images = new Gambar($text, 'dark');
        $text = $images->getResult(
            $this::STORAGE . '/result.jpg',
            'image/jpeg',
            100
        );
        yield $this->messages->sendMedia([
            'peer' => $peer,
            'media' => [
                '_' => 'inputMediaUploadedPhoto',
                'file' => $this::STORAGE . '/result.jpg',
            ],
        ]);
        return round(microtime(true) * 1000 - $start);
    }

    public function onAny($update)
    {
        // on any event
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
            if (preg_match('/^\/animate/', $message)) {
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
            } elseif (preg_match('/^\/eval/i', $message)) {
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
            } elseif (preg_match('/^\/channel/i', $message)) {
                try {
                    preg_match('/\s"(.+)"/i', $message, $match)
                        ? ($text = $match[1])
                        : ($text = Fortune::make());
                    $speed = (yield $this->channelCommand([
                        'peer' => '@BHSecFortune',
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
            yield Logger::log($update, Logger::VERBOSE);
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
