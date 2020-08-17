<?php

namespace App\Tools;

/**
 * Interface: MacroParserInterface
 *
 */
interface MacroParserInterface
{
    /**
     * setMacro
     *
     * @param string $pattern
     * @param string $replacement
     */
    public static function setMacro(string $pattern, string $replacement);
    /**
     * unsetMacro
     *
     * @param string $keys
     */
    public static function unsetMacro(string $keys);

    /**
     * refreshMacro
     *
     */
    public static function refreshMacro();

    /**
     * parseString
     *
     * @param string $string
     * @return string
     */
    public static function parseString(string $string): string;
}
