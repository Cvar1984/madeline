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
**CARD** : `{$card['number']}`
**TYPE** : {$card['type']}
**STATUS** : *Unknown*
**BALANCE** : *Unknown*
VALID;
        } else {
            $text = <<<INVALID
**CARD** : *{$cc}*
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
