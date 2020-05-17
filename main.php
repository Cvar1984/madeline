<?php

require './vendor/autoload.php';

use danog\MadelineProto\EventHandler;

class Mybot extends EventHandler
{
    public $adminId = '905361440';
    public $storage = './assets';

    public function onAny($update)
    {
        if (empty($update['message']['message'])) return;
        $message = $update['message']['message'];
        $chat_id = $update['message']['id'];
        $from_id = $update['message']['from_id'];
        $peer    = $update;

        if (preg_match('/^\/urban/i', $message)) {
            $rapid = new \Cvar1984\Api\RapidApi();

            if (preg_match('/\s"(.+)"/i', $message, $match)) $query = $match[1];
            $data = yield $rapid->urban($query);
            $data = $data->list[0];
            $text = "*definition*: `$data->definition`\n"
                . "*permalink*: `$data->permalink`";

            yield $this->messages->sendMessage(
                [
                    'peer' => $peer,
                    'reply_to_msg_id' => $chat_id,
                    'parse_mode' => 'Markdown',
                    'message' => $text
                ]
            );
        } elseif (preg_match('/^\/simpleimage/i', $message)) {
            preg_match('/\s"(.+)"/i', $message, $match) ? $text = $match[1] : $text = \Wheeler\Fortune\Fortune::make();

            yield $simpleimage = new \Bhsec\SimpleImage\Gambar($text, 'dark');
            $text = yield $simpleimage
                ->getResult($this->storage . '/result.jpg', 'image/jpeg', 100);
            yield $this->messages->sendMedia(
                [
                    'peer' => $peer,
                    'reply_to_msg_id' => $chat_id,
                    'media' => [
                        '_' => 'inputMediaUploadedPhoto',
                        'file' => $this->storage . '/result.jpg',
                    ],
                    'message' => $text,
                ]
            );
        } elseif (preg_match('/^\/fortune/i', $message)) {
            $text = \Wheeler\Fortune\Fortune::make();
            yield $this->messages->sendMessage(
                [
                    'peer' => $peer,
                    'reply_to_msg_id' => $chat_id,
                    'message' => '`' . $text . '`',
                    'parse_mode' => 'Markdown'
                ]
            );
        } elseif (@$from_id == $this->adminId) {
            // admin commmand
            if (preg_match('/^\/animate/', $message)) {
                preg_match('/\s"(.+)"/i', $message, $match) ? $text = $match[1] : $text = \Wheeler\Fortune\Fortune::make();

                $temposleep = 300000;
                $oldtext = "";
                $num = 1;
                $this->messages->editMessage(
                    [
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
                    ]
                );
                usleep($temposleep);
                while ($oldtext != $text) {
                    $oldtext = mb_substr($text, 0, $num);
                    $oldtext1 = $oldtext . "|";
                    $this->messages->editMessage(
                        [
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
                        ]
                    );
                    usleep($temposleep);
                    $this->messages->editMessage(
                        [
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
                        ]
                    );
                    $num++;
                    usleep($temposleep);
                }
            }

            if (preg_match('/^\/loop/i', $message)) {
                preg_match('/"\s(.*\d)/', $message, $match) ? $count = $match[1] : $count = 1;
                preg_match('/^\/loop\s"(.*)"/i', $message, $match) ? $text = $match[1] : $text = 'Can\'t parse argument';

                for ($x = 0; $x < $count; $x++) {
                    yield $this->messages->sendMessage(
                        [
                            'peer' => $peer,
                            'message' => $text,
                        ]
                    );
                }
            } elseif (preg_match('/^\/debug/i', $message)) {
                yield $this->messages->editMessage(
                    [
                        'no_webpage' => true,
                        'peer' => $peer,
                        'id' => $chat_id,
                        'message' => json_encode(
                            $update,
                            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                        ),
                    ]
                );
            }
            if (preg_match('/^\/upfile/i', $message)) {
                preg_match('/\s"(.+)"/', $message, $match) ? $file = $match[1] : $file = $this->storage . '/default.jpg';

                yield $this->messages->sendMedia(
                    [
                        'peer' => $peer,
                        'reply_to_msg_id' => $chat_id,
                        'media' => [
                            '_' => 'inputMediaUploadedDocument',
                            'file' => $file,
                        ],
                        'message' => basename($file) . ' has been uploaded',
                    ]
                );
            } elseif (preg_match('/^\/eval/i', $message)) {
                try {
                    ob_start();
                    eval(substr($message, 6));
                    $text = ob_get_clean();
                } catch (\danog\MadelineProto\Exception | \ParseError $e) {
                    $text = $e->getMessage();
                }
                yield $this->messages->editMessage(
                    [
                        'peer' => $peer,
                        'message' => $text,
                        'id' => $chat_id
                    ]
                );
            }
        }
    }
}
$settings = [
    'logger' => [
        'logger_level' => 0
    ]
];

$MadelineProto = new \danog\MadelineProto\API('session.madeline', $settings);
$MadelineProto->startAndLoop(MyBot::class);
