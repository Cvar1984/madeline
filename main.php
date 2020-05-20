<?php

require './vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Logger;
use danog\MadelineProto\Tools;
use danog\MadelineProto\RPCErrorException;
use danog\MadelineProto\Exception;
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

    public function urban($query)
    {
        $data = RapidApi::urban($query);
        $data = $data->list[0];
        $text = <<<MD
**definition:** *{$data->definition}*
**permalink:** `{$data->permalink}`
MD;
        yield $this->messages->sendMessage([
            'peer' => $this->peer,
            'reply_to_msg_id' => $this->chatId,
            'parse_mode' => 'Markdown',
            'message' => $text,
        ]);
    }
    public function simpleImage($text)
    {
        $images = new Gambar($text, 'dark');
        $text = $images->getResult(
            $this::STORAGE . '/result.jpg',
            'image/jpeg',
            100
        );
        yield $this->messages->sendMedia([
            'peer' => $this->peer,
            'reply_to_msg_id' => $this->chatId,
            'media' => [
                '_' => 'inputMediaUploadedPhoto',
                'file' => $this::STORAGE . '/result.jpg',
            ],
            'message' => $text,
        ]);
    }
    public function fortune()
    {
        $text = Fortune::make();
        yield $this->messages->sendMessage([
            'peer' => $this->peer,
            'reply_to_msg_id' => $this->chatId,
            'message' => '`' . $text . '`',
            'parse_mode' => 'Markdown',
        ]);
    }
    public function animate($text)
    {
        $temposleep = 300000;
        $oldtext = '';
        $num = 1;
        $this->messages->editMessage([
            'no_webpage' => true,
            'peer' => $this->peer,
            'id' => $this->chatId,
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
            $oldtext = mb_substr($text, 0, $num);
            $oldtext1 = $oldtext . "|";
            $this->messages->editMessage([
                'no_webpage' => true,
                'peer' => $this->peer,
                'id' => $this->chatId,
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
            $this->messages->editMessage([
                'no_webpage' => true,
                'peer' => $this->peer,
                'id' => $this->chatId,
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
    public function loopCommand($text, $count)
    {
        for ($x = 0; $x < $count; $x++) {
            if ($text === false) {
                $buff = $text = Fortune::make();
            }

            $this->messages->sendMessage([
                'peer' => $this->peer,
                'message' => $text,
            ]);

            if (isset($buff) && $buff == $text) {
                $text = false;
            }
        }
    }
    public function upfile($file)
    {
        yield $this->messages->sendMedia([
            'peer' => $this->peer,
            'reply_to_msg_id' => $this->chatId,
            'parse_mode' => 'Markdown',
            'media' => [
                '_' => 'inputMediaUploadedDocument',
                'file' => $file,
            ],
            'message' => '*' . basename($file) . '* has been uploaded',
        ]);
    }
    public function evalCommand($text)
    {
        ob_start();
        eval($text);
        $text = ob_get_clean();
        yield $this->messages->editMessage([
            'peer' => $this->peer,
            'message' => $text,
            'id' => $this->chatId,
        ]);
    }
    
    public function onAny($update)
    {
        // on any event
        if (empty($update['message']['message'])) {
            return;
        }
        $this->message = $update['message']['message'];
        $this->chatId = $update['message']['id'];
        $this->fromId = @$update['message']['from_id'];
        $this->peer = $update;

        if (preg_match('/^\/urban/i', $this->message)) {
            try {
                if (preg_match('/\s"(.+)"/i', $this->message, $match)) {
                    $query = $match[1];
                }
                yield $this->urban($query);
            } catch (Exception$e) {
                yield $this->messages->sendMessage([
                    'peer' => $this->peer,
                    'reply_to_msg_id' => $this->chatId,
                    'message' => $e->getMessage(),
                ]);
                // yield $thiz->report($e);
            }
        } elseif (preg_match('/^\/simpleimage/i', $this->message)) {
            preg_match('/\s"(.+)"/i', $this->message, $match)
                ? ($text = $match[1])
                : ($text = Fortune::make());
            yield $this->simpleImage($text);
        } elseif (preg_match('/^\/fortune/i', $this->message)) {
            yield $this->fortune();
        } elseif (@$this->fromId == $this::ADMIN_ID) {
            // admin commmand
            if (preg_match('/^\/animate/', $this->message)) {
                preg_match('/\s"(.+)"/i', $this->message, $match)
                    ? ($text = $match[1])
                    : ($text = Fortune::make());
                yield $this->animate($text);
            } elseif (preg_match('/^\/loop/i', $this->message)) {
                try {
                    preg_match('/"\s(.*\d)/', $this->message, $match)
                        ? ($count = $match[1])
                        : ($count = 1);
                    preg_match('/^\/loop\s"(.*)"/i', $this->message, $match)
                        ? ($text = $match[1])
                        : ($text = false);
                    yield $this->loopCommand($text, $count);
                } catch (Exception $e) {
                    yield $this->messages->editMessage([
                        'peer' => $this->peer,
                        'id' => $this->chatId,
                        'message' => $e->getMessage(),
                    ]);
                    // yield $this->report($e);
                }
            } elseif (preg_match('/^\/upfile/i', $this->message)) {
                try {
                    preg_match('/\s"(.+)"/', $this->message, $match)
                        ? ($file = $match[1])
                        : ($file = $this::STORAGE . '/default.jpg');

                    yield $this->upfile($file);
                } catch (Exception | RPCErrorException $e) {
                    yield $this->messages->editMessage([
                        'peer' => $this->peer,
                        'id' => $this->chatId,
                        'message' => $e->getMessage(),
                    ]);
                    //yield $this->report($e);
                }
            } elseif (preg_match('/^\/eval/i', $this->message)) {
                try {
                    $text = substr($this->message, 6);
                    yield $this->evalCommand($text);
                } catch (Exception|RPCErrorException|ParseError $e) {
                    $text = $e->getMessage();
                    yield $this->messages->editMessage([
                        'peer' => $this->peer,
                        'id' => $this->chatId,
                        'message' => $text,
                    ]);
                    //yield $this->report($e);
                }
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
