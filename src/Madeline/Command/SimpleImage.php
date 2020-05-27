<?php

namespace Cvar1984\Madeline\Command;

use danog\MadelineProto\FileCallback;
use Bhsec\SimpleImage\Templates\QuoteTemplates;

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

        $imagesOption = [
            'text' => $text,
            'watermark' => 'Anonymous',
            'query' => $query,
            'font' => 'Shadow Brush.ttf',
            'result' => [
                'output' => $cache,
                'mime' => 'image/jpeg',
                'quality' => $quality,
            ],
        ];

        $text = json_encode(QuoteTemplates::make($imagesOption),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
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
