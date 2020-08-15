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
    private static $pattern = '~(?:^,([^.]+)|\G(?!^))\.(?= )(?: (\S+))? (.+?(?=\. |$))~';

    /**
     * macros
     *
     * @var mixed
     */
    public static $macros = [];

    /**
     * parseString
     *
     * @param string $command
     * @return array|string
     */
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
    public static function refreshMacro()
    {
        self::$macros = [
            '/%(randInt|rand_int)%/' => rand(),
            '/%(fortune|rand_str)%/' => \Cvar1984\Fortune\Fortune::make(),
        ];
    }

    /**
     * parseMacro
     *
     * @param string $command
     */
    public static function parseMacro(string $command): string
    {
        if (!self::$macros) {
            self::refreshMacro();
        }
        return preg_replace(
            array_keys(self::$macros),
            array_values(self::$macros),
            $command
        );
    }
}
