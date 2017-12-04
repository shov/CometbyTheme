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
 * Interface ThemeInterface
 * The key interface of CometbyTheme theme
 * @see Theme
 * @package  CometbyTheme/Interfaces
 * @category Interface
 * @author   Comet.by
 */
interface ThemeInterface
{
    public static function getPlacement();

    public static function getAjaxHandler();
}