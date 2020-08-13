<?php

namespace App\Event;

/**
 * Class: EventAbstract
 *
 * @abstract
 */
abstract class EventAbstract
{
    /**
     * callstack
     *
     * @var mixed
     */
    protected static $callstack = [];

    /**
     * call
     *
     * @param string $callName
     * @param array $params
     */
    public static function call(string $callName, array $params): \Generator
    {
        if (!array_key_exists($callName, self::$callstack)) {
            throw new \Exception(sprintf('callable %s not found', $callName));
        }
        yield call_user_func_array(self::$callstack[$callName], $params);
    }
    /**
     * register
     *
     * @param string $callName
     * @param callable $call
     */
    public static function register(string $callName, callable $call)
    {
        self::$callstack[$callName] = $call;
    }
}
