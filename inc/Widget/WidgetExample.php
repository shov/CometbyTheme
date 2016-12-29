<?php

/*
 * This file is part of the CometbyTheme package.
 *
 * (c) Alexandr Shevchenko [comet.by] alexandr@comet.by
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CometbyTheme\Widget;

use \WP_Widget;
use CometbyTheme\Theme;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Widget Example
 * @package  CometbyTheme/Widget
 * @category Class
 * @author   Comet.by
 */
class WidgetExample extends WP_Widget
{

    function __construct()
    {
        $aWidgetOps = ['classname' => 'widget_example', 'description' => 'Do Nothing',];
        $aControlOps = ['width' => 250, 'height' => 350, 'id_base' => 'widget_example-widget',];
        parent::__construct('widget_example-widget', 'Widget Example', $aWidgetOps, $aControlOps);
    }

    public function widget($aArgs, $aInstance)
    {
        ?>
        WidgetExample Output
        <?php
    }

    function update($aNewInstance, $aOldInstance)
    {
        $aInstance = $aOldInstance;
        $aInstance = $aNewInstance;
        return $aInstance;
    }

    public function form($aInstance)
    {
        ?>
        Widget Example Config Output
        <?php
    }
}