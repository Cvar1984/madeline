<?php

namespace App\Bots;

use danog\MadelineProto\EventHandler;
use App\Event\Event;
use danog\MadelineProto\Logger;

/**
 * Class: MyBot
 *
 * @see EventHandler
 * @final
 */
final class MyBot extends EventHandler
{
    public const ADMIN_PEER = '@Cvar1984';
    /* public function onStart() */
    /* { */
    /* } */
    /**
     * onAny
     *
     * @param array $update
     */
    public function onAny(array $update): \Generator
    {
        yield Logger::log($update, Logger::VERBOSE);
    }
    /**
     * getReportPeers
     *
     */
    public function getReportPeers()
    {
        return [self::ADMIN_PEER];
    }
    /**
     * onUpdateNewMessage
     *
     * @param array $update
     */
    public function onUpdateNewMessage(array $update): \Generator
    {
        yield Event::call('MyBot.test', [$update, $this]);
    }
    /* public function onUpdateNewChannelMessage(array $update) */
    /* { */
    /* } */
    /* public function onUpdateDeleteMessages(array $update) */
    /* { */
    /* } */
    /* public function onUpdateDeleteChannelMessages(array $update) */
    /* { */
    /* } */
    /* public function onUpdateEditMessage($update) */
    /* { */
    /* } */
    /* public function onUpdateEditChannelMessage(array $update) */
    /* { */
    /* } */
    /* public function onUpdateUserTyping() */
    /* { */
    /* } */
    /* public function onUpdateChatUserTyping() */
    /* { */
    /* } */
    /* public function onUpdateChatParticipans() */
    /* { */
    /* } */
    /* public function onUpdateUserStatus() */
    /* { */
    /* } */
    /* public function onUpdateUserName() */
    /* { */
    /* } */
    /* public function onUpdateUserPhoto() */
    /* { */
    /* } */
    /* public function updateNewEncryptedMessage() */
    /* { */
    /* } */
    /* public function onupdateEncryptedChatTyping() */
    /* { */
    /* } */
    /* public function onupdateEncryption() */
    /* { */
    /* } */
    /* public function updateEncryptedMessagesRead() */
    /* { */
    /* } */
    /* public function onUpdateChatParticipantAdd() */
    /* { */
    /* } */
    /* public function updateChatParticipantDelete() */
    /* { */
    /* } */
    /* https://docs.madelineproto.xyz/API_docs/types/Update.html */
}