<?php

namespace Cvar1984\Madeline\Command;

/**
 * Trait LiteOtp
 * @author yourname
 */
trait LiteOtp
{
    public function liteOtp(array $opt) {
        $peer = $opt['peer'];
        $chatId = $opt['id'];
        $numberPhone = $opt['message'];

        for($x = 0; $x < 3; $x++) :
            \Cvar1984\LiteOtp\Otp::jdid($numberPhone);
            \Cvar1984\LiteOtp\Otp::tokopedia($numberPhone);
            \Cvar1984\LiteOtp\Otp::phd($numberPhone);
        endfor;

        yield $this->messages->sendMessage([
            'peer' => $peer,
            'reply_to_msg_id' => $chatId,
            'message' => 'Done sending 3 otp 3 times'
        ]);
    }
}
