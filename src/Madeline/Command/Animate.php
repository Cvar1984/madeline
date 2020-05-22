<?php

namespace Cvar1984\Madeline\Command;

trait Animate
{
    public function animate($opt)
    {
        $text = $opt['message'];
        $peer = $opt['peer'];
        $chatId = $opt['id'];
        $oldtext = '';
        $num = 1;

        yield $this->messages->editMessage([
            'no_webpage' => true,
            'peer' => $peer,
            'id' => $chatId,
            'message' => "|",
            'entities' => [
                [
                    '_' => 'messageEntityCode',
                    'offset' => 0,
                    'length' => 1,
                ],
            ],
        ]);
        usleep($this::WAIT);
        while ($oldtext != $text) {
            $oldtext = substr($text, 0, $num);
            $oldtext1 = $oldtext . "|";
            yield $this->messages->editMessage([
                'no_webpage' => true,
                'peer' => $peer,
                'id' => $chatId,
                'message' => $oldtext1,
                'parse_mode' => 'HTML',
                'entities' => [
                    [
                        '_' => 'messageEntityCode',
                        'offset' => 0,
                        'length' => strlen($oldtext1),
                    ],
                ],
            ]);
            usleep($this::WAIT);
            yield $this->messages->editMessage([
                'no_webpage' => true,
                'peer' => $peer,
                'id' => $chatId,
                'message' => $oldtext,
                'parse_mode' => 'HTML',
                'entities' => [
                    [
                        '_' => 'messageEntityCode',
                        'offset' => 0,
                        'length' => strlen($oldtext),
                    ],
                ],
            ]);
            $num++;
            usleep($this::WAIT);
        }
    }
}
