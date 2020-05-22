<?php
namespace Cvar1984\Madeline\Command;

use danog\MadelineProto\FileCallback;

trait Upfile
{
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
}
