<?php
namespace Cvar1984\Madeline\Command;

use Wheeler\Fortune\Fortune;

trait Loop
{
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
}
