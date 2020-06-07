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
            'media' => [
                '_' => 'inputMediaUploadedDocument',
                'file' => new FileCallback($file, function ($progress) use (
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
                'attributes' => [
                    [
                        '_' => 'documentAttributeFilename',
                        'file_name' => basename($file),
                    ],
                ],
            ]
        ]);
    }
}
