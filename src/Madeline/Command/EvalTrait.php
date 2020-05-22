<?php

namespace Cvar1984\Madeline\Command;

trait EvalTrait
{
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
}
