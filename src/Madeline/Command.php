<?php
namespace Cvar1984\Madeline;

use danog\MadelineProto\EventHandler;

abstract class Command extends EventHandler
{
    public const ADMIN_ID = '905361440';
    public const ADMIN_PEER = '@Cvar1984';
    public const CHANNEL_PEER = 'BHSecFortune';
    public const STORAGE = './assets';
    public const WAIT = 500000;

    use \Cvar1984\Madeline\Command\SimpleImage;
    use \Cvar1984\Madeline\Command\Urban;
    use \Cvar1984\Madeline\Command\Animate;
    use \Cvar1984\Madeline\Command\Loop;
    use \Cvar1984\Madeline\Command\Upfile;
    use \Cvar1984\Madeline\Command\EvalTrait;
    use \Cvar1984\Madeline\Command\Channel;
    use \Cvar1984\Madeline\Command\Fortune;
    use \Cvar1984\Madeline\Command\Checker;
}
