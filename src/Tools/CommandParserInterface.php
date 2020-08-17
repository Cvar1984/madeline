<?php

namespace App\Tools;

interface CommandParserInterface
{
    /**
     * parseString
     *
     * @param string $string
     * @return array|string
     */
    public static function parseString(string $string);
}
