<?php
namespace Cvar1984\Madeline\Command;

use Cvar1984\Api\RapidApi;

trait Urban
{
    public function urban($opt)
    {
        $text = $opt['message'];
        $peer = $opt['peer'];
        $chatId = $opt['id'];
        $data = RapidApi::urban($text);
        $data = $data->list[0];
        $text = <<<MD
**definition:** *{$data->definition}*
[permalink]({$data->permalink})
MD;
        yield $this->messages->sendMessage([
            'peer' => $peer,
            'reply_to_msg_id' => $chatId,
            'parse_mode' => 'Markdown',
            'message' => $text,
        ]);
    }
}
