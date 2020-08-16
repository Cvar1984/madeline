<?php

namespace App\Tools;

/**
 * Class: CommandParser
 *
 */
class CommandParser
{
    /**
     * pattern
     *
     * @var string
     */
    protected static $pattern = '~(?:^,([^.]+)|\G(?!^))\.{1}(?= )(?: (\S+))? (.+?(?=\. |$))~';
    /* /myCommand,, color black,, text hello, world.,, size 20px */
    public static function parseString(string $command)
    {
        if (preg_match_all(self::$pattern, $command, $out)) {
            foreach ($out[2] as $index => $subKey) {
                if (strlen($subKey)) {
                    $result[lcfirst($out[1][0])][lcfirst($subKey)] =
                        $out[3][$index];
                } else {
                    $result[lcfirst($out[1][0])] = $out[3][$index];
                }
            }
        }
        return isset($result) ? $result : $command;
    }
}
