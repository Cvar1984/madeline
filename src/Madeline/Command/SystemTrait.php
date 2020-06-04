<?php

namespace Cvar1984\Madeline\Command;

/**
 * Trait SystemTrait
 * @author yourname
 */
trait SystemTrait
{
    protected static string $using;
    protected static function getSystem($cmd)
    {
        if (function_exists('system')) {
            self::$using = 'system';
            ob_start();
            system($cmd . ' 2>&1');
            return ob_get_clean();
        } elseif (!function_exists('exec')) {
            self::$using = 'exec';
            exec($cmd . ' 2>&1', $out, $ret);

            ob_start();
            foreach ($out as $key => $var) {
                echo $var . PHP_EOL;
            }
            return ob_get_clean();
        } elseif (function_exists('shell_exec')) {
            self::$using = 'shell_exec';
            return shell_exec($cmd . ' 2>&1');
        } elseif (function_exists('passthru')) {
            self::$using = 'passthru';
            ob_start();
            passthru($cmd);
            return ob_get_clean();
        } elseif (function_exists('proc_open')) {
            self::$using = 'proc_open';

            $descriptorspec = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ];

            $proc = proc_open($cmd, $descriptorspec, $pipes);
            $out = stream_get_contents($pipes[1]);
            $out .= stream_get_contents($pipes[2]);
            proc_close($proc);
            return $out;
        } elseif (function_exists('popen')) {
            self::$using = 'popen';
            $ph = popen($cmd . ' 2>&1', 'r');
            pclose($ph);
            return fread($ph, 1024);
        } else {
            self::$using = false;
            return 'There is no available function to use';
        }
    }
    public function systemCommand(array $opt): \Generator
    {
        $command = $opt['message'];
        $chatId = $opt['id'];
        $peer = $opt['peer'];
        $text = self::getSystem($command);
        $using = self::$using;

        $text = <<<TXT
command: <i>{$command}</i>
using: <i>{$using}</i>

<pre>{$text}</pre>
TXT;
        yield $this->messages->editMessage([
            'peer' => $peer,
            'id' => $chatId,
            'parse_mode' => 'HTML',
            'message' => $text,
        ]);
    }
}
