<?php

namespace Cvar1984\Madeline;

use danog\MadelineProto\FileCallback;
use Cvar1984\Api\RapidApi;
use Bhsec\SimpleImage\Gambar;
use Wheeler\Fortune\Fortune;

abstract class Command extends CommandLayer
{
    public const ADMIN_ID = '905361440';
    public const ADMIN_PEER = 'Cvar1984';
    public const CHANNEL_PEER = 'BHSecFortune';
    public const STORAGE = './assets';
    public const WAIT = 300000;
 
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
                        usleep($this::WAIT);
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
        usleep($this::WAIT);
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
            usleep($this::WAIT);
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
            usleep($this::WAIT);
        }
    }
    public function loopCommand($opt)
    {
        $start = microtime(true) * 1000;
        $text = $opt['message'];
        $peer = $opt['peer'];
        $chatId = $opt['id'];
        $count = $opt['count'];

        for ($x = 0; $x < $count; $x++) {
            $text === false
                ? ($buff = $text = Fortune::make())
                : ($buff = null);

            $this->messages->sendMessage([
                'peer' => $peer,
                'message' => $text,
            ]);

            if ($buff == $text) {
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
                'file' => new FileCallback(
                    $file,
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
}
