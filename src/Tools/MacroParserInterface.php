<?php

namespace App\Tools;

interface MacroParserInterface
{
    public static function setMacro();
    public static function unsetMacro();
    public static function refreshMacro();
    public static function parseString();
}
