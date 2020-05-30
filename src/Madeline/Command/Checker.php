<?php

namespace Cvar1984\Madeline\Command;

trait Checker
{
    public function isValidCreditCard($opt)
    {
        $peer = $opt['peer'];
        $chatId = $opt['id'];
        $cc = $opt['message'];

        $card = \Inacho\CreditCard::validCreditCard($cc);

        if ($card['valid']) {
            $text = <<<VALID
**LIVE** : *{$card['number']}*
**TYPE** : *{$card['type']}*
VALID;
        } else {
            $text = <<<INVALID
**DIE** : *{$cc}*
*this card is invalid*
INVALID;
        }

        yield $this->messages->sendMessage([
            'peer' => $peer,
            'reply_to_msg_id' => $chatId,
            'message' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }
}
