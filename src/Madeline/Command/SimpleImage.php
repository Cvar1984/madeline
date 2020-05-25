<?php

namespace Cvar1984\Madeline\Command;

use danog\MadelineProto\FileCallback;
use Bhsec\SimpleImage\Templates\BhsecTemplates;

trait SimpleImage
{
    public function simpleImage(array $opt)
    {
        $text = $opt['message'];
        $peer = $opt['peer'];
        $chatId = $opt['id'];
        $quality = $opt['quality'];
        $query = $opt['query'];
        $cache = $this::STORAGE . '/' . sha1(time()) . '.jpg';

        $imagespOption = [
            'text' => $text,
            'query' => $query,
            'font' => 'FSEX300.ttf',
            'result' => [
                'output' => $cache,
                'mime' => 'image/jpeg',
                'quality' => $quality,
            ],
        ];

        $text = BhsecTemplates::make($imagespOption);
        yield $this->messages->sendMedia([
            'peer' => $peer,
            'reply_to_msg_id' => $chatId,
            'media' => [
                '_' => 'inputMediaUploadedPhoto',
                'file' => new FileCallback($cache, function ($progress) use (
                    $peer,
                    $chatId
                ) {
                    yield $this->messages->editMessage([
                        'peer' => $peer,
                        'id' => $chatId,
                        'message' => 'Upload progress: ' . $progress . '%',
                    ]);
                    usleep($this::WAIT);
                }),
            ],
            'message' => $text,
        ]);
    }
}
