<?php

namespace Cvar1984\Madeline;

use danog\MadelineProto\EventHandler;

abstract class CommandLayer extends EventHandler
{
    abstract public function urban($opt);
    abstract public function simpleImage($opt);
    abstract public function fortune($opt);
    abstract public function animate($opt);
    abstract public function loopCommand($opt);
    abstract public function upfile($opt);
    abstract public function evalCommand($opt);
    abstract public function channelCommand($opt);
}
