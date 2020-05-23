<?php

namespace Cvar1984\Madeline\Layer;

use Cvar1984\Madeline\Layer\CommandLayer;

abstract class Command extends CommandLayer
{
    use \Cvar1984\Madeline\Command\SimpleImage;
    use \Cvar1984\Madeline\Command\Urban;
    use \Cvar1984\Madeline\Command\Animate;
    use \Cvar1984\Madeline\Command\Loop;
    use \Cvar1984\Madeline\Command\Upfile;
    use \Cvar1984\Madeline\Command\EvalTrait;
    use \Cvar1984\Madeline\Command\Channel;
    use \Cvar1984\Madeline\Command\Fortune;
}
