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
    public static $pattern = '~(?:^,([^.]+)|\G(?!^))\.(?= )(?: (\S+))? (.+?(?=\. |$))~';

    /**
     * parserString
     *
     * @param string $string
     * @return array|string
     */
    public static function parseString(string $string)
    {
        if (preg_match_all(self::$pattern, $string, $out)) {
            foreach ($out[2] as $index => $subKey) {
                if (strlen($subKey)) {
                    $result[$out[1][0]][$subKey] = $out[3][$index];
                } else {
                    $result[$out[1][0]] = $out[3][$index];
                }
            }
        }
        return isset($result) ? $result : $string;
    }
}
