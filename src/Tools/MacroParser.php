<?php

namespace App\Tools;

class MacroParser
{
    /**
     * macros
     *
     * @var mixed
     */
    protected static $macros = [];

    /**
     * refreshMacro
     *
     */
    public static function refreshMacro()
    {
        self::$macros = [
            '/%(randInt|rand_int)%/' => rand(),
            '/%(fortune|rand_str)%/' => \Cvar1984\Fortune\Fortune::make(),
        ];
    }

    /**
     * setMacro
     *
     * @param mixed $pattern
     * @param mixed $replace
     */
    public static function setMacro($pattern, $replace)
    {
        if (!array_key_exists($pattern, self::$macros)) {
            self::$macros[$pattern] = $replace;
        }
    }

    /**
     * parseString
     *
     * @param string $command
     */
    public static function parseString(string $command): string
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
