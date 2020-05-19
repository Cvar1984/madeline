<?php

require './vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\EventHandler;
use Cvar1984\Api\RapidApi;
use Bhsec\SimpleImage\Gambar;
use Wheeler\Fortune\Fortune;

class Mybot extends EventHandler
{
    protected $adminId = '905361440';
    public const STORAGE = './assets';

    public function onAny($update)
    {
        if (empty($update['message']['message'])) {
            return;
        }
        $message = $update['message']['message'];
        $chat_id = $update['message']['id'];
        $from_id = @$update['message']['from_id'];
        $peer = $update;

        if (preg_match('/^\/urban/i', $message)) {
            if (preg_match('/\s"(.+)"/i', $message, $match)) {
                $query = $match[1];
            }
            try {
                $data = (yield RapidApi::urban($query));
                $data = $data->list[0];
                $text = <<<MD
**definition:** *{$data->definition}*
**permalink:** `{$data->permalink}`
MD;
                yield $this->messages->sendMessage([
                    'peer' => $peer,
                    'reply_to_msg_id' => $chat_id,
                    'parse_mode' => 'Markdown',
                    'message' => $text,
                ]);
            } catch (\danog\MadelineProto\Exception | \Exception $e) {
                yield $this->messages->editMessage([
                    'peer' => $peer,
                    'id' => $chat_id,
                    'message' => $e->getMessage(),
                ]);
            }
        } elseif (preg_match('/^\/simpleimage/i', $message)) {
            preg_match('/\s"(.+)"/i', $message, $match)
                ? ($text = $match[1])
                : ($text = Fortune::make());

            yield ($simpleimage = new Gambar($text, 'dark'));
            $text = (yield $simpleimage->getResult(
                $this::STORAGE . '/result.jpg',
                'image/jpeg',
                100
            ));
            yield $this->messages->sendMedia([
                'peer' => $peer,
                'reply_to_msg_id' => $chat_id,
                'media' => [
                    '_' => 'inputMediaUploadedPhoto',
                    'file' => $this::STORAGE . '/result.jpg',
                ],
                'message' => $text,
            ]);
        } elseif (preg_match('/^\/fortune/i', $message)) {
            $text = Fortune::make();
            yield $this->messages->sendMessage([
                'peer' => $peer,
                'reply_to_msg_id' => $chat_id,
                'message' => '`' . $text . '`',
                'parse_mode' => 'Markdown',
            ]);
        } elseif (@$from_id == $this->adminId) {
            // admin commmand
            if (preg_match('/^\/animate/', $message)) {
                preg_match('/\s"(.+)"/i', $message, $match)
                    ? ($text = $match[1])
                    : ($text = Fortune::make());

                $temposleep = 300000;
                $oldtext = "";
                $num = 1;
                $this->messages->editMessage([
                    'no_webpage' => true,
                    'peer' => $peer,
                    'id' => $chat_id,
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
                        'peer' => $peer,
                        'id' => $chat_id,
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
                        'peer' => $peer,
                        'id' => $chat_id,
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
            } elseif (preg_match('/^\/loop/i', $message)) {
                try {
                    if (preg_match('/"\s(.*\d)/', $message, $match)) {
                        $count = $match[1];
                    }
                    if (preg_match('/^\/loop\s"(.*)"/i', $message, $match)) {
                        $text = $match[1];
                    }

                    for ($x = 0; $x < $count; $x++) {
                        yield $this->messages->sendMessage([
                            'peer' => $peer,
                            'message' => $text,
                        ]);
                    }
                } catch (\danog\MadelineProto\Exception | Exception $e) {
                    yield $this->messages->sendMessage([
                        'peer' => $peer,
                        'id' => $chat_id,
                        'message' => $e->getMessage(),
                    ]);
                }
            } elseif (preg_match('/^\/debug/i', $message)) {
                yield $this->messages->editMessage([
                    'no_webpage' => true,
                    'peer' => $peer,
                    'id' => $chat_id,
                    'message' => json_encode(
                        $update,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                    ),
                ]);
            } elseif (preg_match('/^\/upfile/i', $message)) {
                if (preg_match('/\s"(.+)"/', $message, $match)) {
                    $file = $match[1];
                }
                try {
                    yield $this->messages->sendMedia([
                        'peer' => $peer,
                        'reply_to_msg_id' => $chat_id,
                        'media' => [
                            '_' => 'inputMediaUploadedDocument',
                            'file' => $file,
                        ],
                        'message' => basename($file) . ' has been uploaded',
                    ]);
                } catch (\danog\MadelineProto\Exception | Exception $e) {
                    yield $this->messages->editMessage([
                        'peer' => $peer,
                        'id' => $chat_id,
                        'message' => $e->getMessage(),
                    ]);
                }
            } elseif (preg_match('/^\/eval/i', $message)) {
                try {
                    ob_start();
                    eval(substr($message, 6));
                    $text = ob_get_clean();
                } catch (\danog\MadelineProto\Exception | \ParseError $e) {
                    $text = $e->getMessage();
                    $this->logger($text);
                }
                yield $this->messages->editMessage([
                    'peer' => $peer,
                    'message' => $text,
                    'id' => $chat_id,
                ]);
            } elseif (preg_match('/^\/invite/i', $message)) {
                try {
                    $dialogs = (yield $this->getDialogs());
                    foreach ($dialogs as $dialog) {
                        if (isset($dialog['user_id'])) {
                            $dialogId[] = $dialog['user_id'];
                        }
                    }

                    preg_match('/\s"(.+)"/i', $message, $match)
                        ? ($users = [$match[1]])
                        : ($users = $dialogId);

                    yield $this->channels->inviteToChannel([
                        'channel' => '@BHSecFortune',
                        'users' => $users,
                    ]);
                } catch (\danog\MadelineProto\Exception | \Exception $e) {
                    yield $this->messages->editMessage([
                        'peer' => $peer,
                        'id' => $chat_id,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
            $this->logger($update);
        }
    }
}

$settings = [
    'logger' => [
        'param' => MyBot::STORAGE . '/Madeline.log',
        'logger_level' => 8,
    ],
    'max_tries' => [
        'query' => 1,
    ],
];

$MadelineProto = new API('session.madeline', $settings);
$MadelineProto->startAndLoop(MyBot::class);
