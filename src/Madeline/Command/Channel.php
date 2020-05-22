<?php
namespace Cvar1984\Madeline\Command;

use Bhsec\SimpleImage\Gambar;

trait Channel
{
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
