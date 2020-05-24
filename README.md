# madeline
Personal MTProto Telegram User BOT
> click for demo
<a href="https://youtu.be/N4ZI5xgf0dA">
    <img src="assets/default.jpg" width="200px" height="200px"/>
</a>

![PHP Composer](https://github.com/Cvar1984/madeline/workflows/PHP%20Composer/badge.svg?branch=master)
[![CodeFactor](https://www.codefactor.io/repository/github/cvar1984/madeline/badge)](https://www.codefactor.io/repository/github/cvar1984/madeline)


## how to create a command
Register command on event handler
```php
<?php
// src/Madeline/Command.php
namespace Cvar1984\Madeline;

use danog\MadelineProto\EventHandler;

abstract class Command extends EventHandler
{
    // public static properties or whatever
    public const STORAGE = './assets';
    use \Cvar1984\Madeline\Command\SimpleImage;
}
```
Now create a command
```php
<?php
// src/Madeline/Command/SimpleImage.php
namespace Cvar1984\Madeline\Command;

use Bhsec\SimpleImage\Gambar;

trait SimpleImage
{
    public function commandSimpleImage($opt)
    {
        $text = $opt['message'];
        $peer = $opt['peer'];

        $start = microtime(true) * 1000;
        $images = new Gambar($text, 'dark');
        $text = $images->getResult(
            $this::STORAGE . '/result.jpg',
            'image/jpeg',
            100
        );
        yield $this->messages->sendMedia([
                'peer' => $peer,
                'media' => [
                '_' => 'inputMediaUploadedPhoto',
                'file' => $this::STORAGE . '/result.jpg',
                ],
        ]);
        return round(microtime(true) * 1000 - $start);
    }
}
```
Finnaly use them
```php
<?php

require './vendor/autoload.php';

use danog\MadelineProto\API;
use Cvar1984\Madeline\Command;

class Mybot extends Command
{
    public function onAny($update)
    {
        yield $this->commandSimpleImage([
            'peer' => $update,
            'text' => \Wheeler\Fortune\Fortune::make()
         ]);
    }
}
$settings = [
    'logger' => [
        'param' => MyBot::STORAGE . '/Madeline.log',
    ],
    'max_tries' => [
        'query' => 1,
    ],
];

$MadelineProto = new API('session.madeline', $settings);
$MadelineProto->async(true);
$MadelineProto->startAndLoop(MyBot::class);
```
That's it
