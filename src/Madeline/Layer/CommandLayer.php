<?php

namespace Cvar1984\Madeline\Layer;

use danog\MadelineProto\EventHandler;

abstract class CommandLayer extends EventHandler
{
    public const ADMIN_ID = '905361440';
    public const ADMIN_PEER = 'Cvar1984';
    public const CHANNEL_PEER = 'BHSecFortune';
    public const STORAGE = './assets';
    public const WAIT = 300000;
}
