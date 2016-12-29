<?php

/*
 * This file is part of the CometbyTheme package.
 *
 * (c) Alexandr Shevchenko [comet.by] alexandr@comet.by
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CometbyTheme;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Singleton Trait
 * @version   1.0
 * @package   CometbyTheme
 * @category  Trait
 * @author    Comet.by
 */
trait SingletonTrait
{
    private static $_inst;

    public static function getInstance()
    {
        return isset(static::$_inst)
            ? static::$_inst
            : static::$_inst = new static;
    }

    private function __construct()
    {
    }

    final private function __wakeup()
    {
    }

    final private function __clone()
    {
    }
}