<?php

/*
 * This file is part of the CometbyTheme package.
 *
 * (c) Alexandr Shevchenko [comet.by] alexandr@comet.by
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace CometbyTheme\Core;

use CometbyTheme\SingletonTrait;
use CometbyTheme\EntityLoaderTrait;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Widget Controller
 * Wrapper for resources load operations, contents most of them
 * @package  CometbyTheme/Core
 * @category Class
 * @author   Comet.by
 */
class WidgetController
{
    use SingletonTrait;
    use EntityLoaderTrait;

    const PATH = "Widget";
    const SIDEBAR = 'p-sidebar';
    protected $aEntities;

    public static function init()
    {
        return self::getInstance();
    }

    private function __construct()
    {
        $this->aEntities = [];

        if (!function_exists('register_sidebar')) throw new \Exception(sprintf("Have a wrong environment cant find register_sidebar()! Is it really WP?"));
        register_sidebar([
            'name' => 'Боковая колонка',
            'id' => self::SIDEBAR,
            'class' => '',
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '',
            'after_title' => '',
        ]);

        $this->entitiesAutoload(self::PATH);

        $self = $this;
        add_action('widgets_init', function () use ($self) {
            $self->registerWidget();
        });
    }

    protected function registerWidget()
    {
        /* Remove default */
        unregister_widget('WP_Widget_Archives');
        unregister_widget('WP_Widget_Calendar');
        unregister_widget('WP_Widget_Categories');
        unregister_widget('WP_Widget_Meta');
        unregister_widget('WP_Widget_Pages');
        unregister_widget('WP_Widget_Recent_Comments');
        unregister_widget('WP_Widget_Recent_Posts');
        unregister_widget('WP_Widget_RSS');
        unregister_widget('WP_Widget_Search');
        unregister_widget('WP_Widget_Tag_Cloud');
        unregister_widget('WP_Widget_Text');
        unregister_widget('WP_Nav_Menu_Widget');

        assert(is_array($this->aEntities));
        foreach ($this->aEntities as $widget) {
            register_widget($widget);
        }
    }

    public function drawSideBar()
    {
        dynamic_sidebar(self::SIDEBAR);
    }
}