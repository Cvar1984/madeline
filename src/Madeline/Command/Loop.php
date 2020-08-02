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
        $text = empty($text) ? Fortune::make() : $text;

        $macro['pattern'][0] = '/\[rand_int\]/';
        $macro['pattern'][1] = '/\[rand_str\]/';
        $macro['pattern'][2] = '/\[fortune\]/';

        for ($x = 0; $x < $count; $x++) {
            $macro['replacement'][0] = rand();
            $macro['replacement'][1] = bin2hex(random_bytes(16));
            $macro['replacement'][2] = Fortune::make();
            $macroedText = preg_replace($macro['pattern'], $macro['replacement'], $text);
            $this->messages->sendMessage([
                'peer' => $peer,
                'message' => $macroedText,
            ]);
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
