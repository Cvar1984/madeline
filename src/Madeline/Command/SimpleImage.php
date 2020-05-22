<?php

namespace Cvar1984\Madeline\Command;
use danog\MadelineProto\FileCallback;
use Bhsec\SimpleImage\Gambar;

trait SimpleImage
{
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
            'message' => $text,
        ]);
    }
}
