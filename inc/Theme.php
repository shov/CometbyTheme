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

use CometbyTheme\Core\AdminConf;
use CometbyTheme\Core\ResourcesLoader;
use CometbyTheme\Core\Template;
use CometbyTheme\Core\WidgetController;
use CometbyTheme\Core\ThemeFormater;
use CometbyTheme\Core\ThemePlacement;
use CometbyTheme\Core\ThemeCustomizer;
use CometbyTheme\Core\ThemeAjaxHandler;
use Mustache_Engine;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Theme
 * The key class of CometbyTheme theme
 * @see ThemeInterface
 * @package  CometbyTheme
 * @category Class
 * @author   Comet.by
 */
final class Theme implements ThemeInterface
{
    private static $_inst;

    public static function init()
    {
        if (self::$_inst === null) {
            self::$_inst = new self();
        }
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    const PREFIX = "macho_";
    const ROOT = __DIR__;
    private $themeFormater;
    private $themePlacement;
    private $themeAjaxHandler;
    private $themeCustomizer;
    private $resourcesLoader;

    /**
     * @var Template $template
     */
    private $template;

    /**
     * Theme constructor.
     */
    private function __construct()
    {
        @date_default_timezone_set("Europe/Minsk");

        AdminConf::init();
        WidgetController::init();
        $this->template = Template::getInstance();
        $this->themeFormater = ThemeFormater::getInstance();
        $this->themePlacement = ThemePlacement::getInstance();
        $this->themeCustomizer = ThemeCustomizer::getInstance();
        $this->themeAjaxHandler = ThemeAjaxHandler::getInstance(['prefix' => self::PREFIX,]);
        $this->resourcesLoader = ResourcesLoader::getInstance();
        $this->makeThemeSetup();
    }

    /**
     *  Some custom config
     */
    private function makeThemeSetup()
    {
        //add_theme_support('post-thumbnails');
    }

    /**
     * Provide access to ThemeFormater
     * @return ThemeFormater Object Instance
     */
    public static function getFormater()
    {
        self::init();
        return self::$_inst->themeFormater;
    }

    /**
     * Provide access to ThemePlacement
     * @return ThemePlacement Object Instance
     */
    public static function getPlacement()
    {
        self::init();
        return self::$_inst->themePlacement;
    }

    /**
     * Provide access to ThemeCustomizer
     * @return ThemeCustomizer Object Instance
     */
    public static function getCustomizer()
    {
        self::init();
        return self::$_inst->themeCustomizer;
    }

    /**
     * Provide access to ThemeAjaxHandler
     * @return ThemeAjaxHandler Object Instance
     */
    public static function getAjaxHandler()
    {
        self::init();
        return self::$_inst->themeAjaxHandler;
    }

    /**
     * Provide access to Template
     * @return Template
     */
    public static function getTemplate()
    {
        static::init();
        return static::$_inst->template;
    }

    /**
     * Provide access to ResourcesLoader
     * @return ResourcesLoader
     */
    public static function getResourcesLoader()
    {
        static::init();
        return static::$_inst->resourcesLoader;
    }
}