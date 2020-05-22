<?php
namespace Cvar1984\Madeline\Command;

trait Fortune
{
    public function fortune($opt)
    {
        $peer = $opt['peer'];
        $chatId = $opt['id'];
        $text = \Wheeler\Fortune\Fortune::make();

        yield $this->messages->sendMessage([
            'peer' => $peer,
            'reply_to_msg_id' => $chatId,
            'message' => '`' . $text . '`',
            'parse_mode' => 'Markdown',
        ]);
    }
}
