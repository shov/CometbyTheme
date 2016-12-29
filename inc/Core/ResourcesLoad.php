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

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ResourcesLoad
 * Wrapper for resources load operations, contents most of them
 * @package  CometbyTheme/Core
 * @category Class
 * @author   Comet.by
 */
class ResourcesLoad
{
    use SingletonTrait;

    public static function init()
    {
        return self::getInstance();
    }

    private $basePath;
    private $jsPath;
    private $cssPath;
    private $incPath;

    /**
     * ResourcesLoad constructor.
     * @param string $basePath
     */
    private function __construct()
    {
        $this->basePath = get_template_directory_uri();

        $this->jsPath = $this->basePath . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR;
        $this->cssPath = $this->basePath . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR;
        $this->incPath = $this->basePath . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR;

        $self = $this;
        add_action('wp_enqueue_scripts', function () use ($self) {
            $self->enqueueScripts();
        });
    }

    /**
     * @param $handle
     * @param bool $src
     * @param array $deps
     * @param bool $ver
     * @return bool
     */
    private function regScriptFooter($handle, $src = false, $deps = array(), $ver = false)
    {
        return wp_register_script($handle, $src, $deps, $ver, true);
    }

    /**
     * @param $handle
     * @param bool $src
     * @param array $deps
     * @param bool $ver
     * @return bool
     */
    private function regScriptHeader($handle, $src = false, $deps = array(), $ver = false)
    {
        return wp_register_script($handle, $src, $deps, $ver, false);
    }

    /**
     * @param string $name
     * @return null
     */
    private function addIncAjaxToScript($name = '')
    {
        $name = (string)$name;
        if (empty(!$name)) return;
        wp_localize_script($name, 'incAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
        return;
    }

    /**
     * @return null
     */
    private function enqueueScripts()
    {
        /* Стили */
        wp_register_style('global_cometby', $this->cssPath . 'fx_global.css');
        wp_enqueue_style('global_cometby');

        /* Стили блоков */
        wp_register_style('slick', $this->incPath . 'slick-carousel/slick/slick.css');
        wp_register_style('slick_theme', $this->incPath . 'slick-carousel/slick/slick-theme.css');

        wp_enqueue_style('slick');
        wp_enqueue_style('slick_theme');

        /* Скриптики */
        $this->regScriptHeader('dollar_jq', $this->jsPath . 'jquery-2.1.4.min.js');

        $this->regScriptFooter('slick', $this->incPath . 'slick-carousel/slick/slick.min.js', ['dollar_jq',]);
        $this->regScriptFooter('fx_library', $this->jsPath . 'fx_library.js', ['dollar_jq',]);

        $this->regScriptFooter('google_maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCwt9mufzj5w7T_fDfu7E2Ya5CHndD8dIM');
        $this->regScriptFooter('fx_handler', $this->jsPath . 'fx_handler.js', ['dollar_jq', 'slick', 'fx_library', 'google_maps']);

        /* Внедрение Ajax обработчика */
        $this->addIncAjaxToScript('fx_handler');

        /*Данные для балуна на карте*/
        wp_localize_script('fx_handler', 'objCustomBaloon', ['img' => $this->basePath . '/img/marker-map.png']);

        wp_enqueue_script('dollar_jq');
        wp_enqueue_script('slick');
        wp_enqueue_script('google_maps');
        wp_enqueue_script('fx_library');
        wp_enqueue_script('fx_handler');

        global $post; //need to work with placement here
        //the_post(); ?
        if ('kontaktyi' === $post->post_name) {
            wp_enqueue_script('google_maps');
        }
        //wp_reset_postdata(); ?
        //rewind_posts(); ?

        return;
    }
}