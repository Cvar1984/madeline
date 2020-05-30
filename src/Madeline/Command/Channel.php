<?php
namespace Cvar1984\Madeline\Command;

use Bhsec\SimpleImage\Templates\BhsecTemplates;

trait Channel
{
    public function channelCommand($opt)
    {
        $start = microtime(true) * 1000;
        $text = $opt['message'];
        $peer = $opt['peer'];
        $chatId = $opt['id'];
        $quality = $opt['quality'];
        $query = $opt['query'];
        $cache = $this::STORAGE . '/' . sha1(time()) . '.jpg';

        $imagesOption = [
            'text' => $text,
            'query' => $query,
            'font' => 'FSEX300.ttf',
            'result' => [
                'output' => $cache,
                'mime' => 'image/jpeg',
                'quality' => $quality,
            ],
        ];

        $text = BhsecTemplates::make($imagesOption);

        yield $this->messages->sendMedia([
            'peer' => $peer,
            'media' => [
                '_' => 'inputMediaUploadedPhoto',
                'file' => $cache,
            ],
        ]);
        return round(microtime(true) * 1000 - $start);
    }
}
