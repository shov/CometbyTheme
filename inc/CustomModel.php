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

use stdClass;
use WP_Query;
use WP_Post;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * CometbyTheme helper to provide business data through WP
 *
 * Class ModelHelper
 * @version   1.0
 * @package   CometbyTheme
 * @category  Class
 * @author    Comet.by
 */
class CustomModel extends WP_Query
{
    /**
     * @var WP_Query $wpQuery
     */
    protected $wpQuery = null;

    /**
     * @var array $baseData
     */
    protected static $baseData = [];

    /**
     * @var array $data
     */
    protected $data = [];

    /**
     * Adds some data to base data of all objects which will be created after this
     * @param array $data
     */
    public static function addBaseVariables(array $data = [])
    {
        static::$baseData = array_merge(static::$baseData, $data);
    }

    /**
     * CustomModel constructor.
     * @param string $query
     */
    public function __construct($query = '')
    {
        $this->addVariables(static::$baseData);

        if (!empty($query) && (is_string($query) || is_array($query))) {
            $this->wpQuery = new WP_Query($query);
        }
    }

    /**
     * Fetch the data. Use it after adds chain
     * @return array
     */
    public function fetch()
    {
        return $this->data;
    }

    /**
     * @return $this
     */
    public function addWpEnv()
    {
        $data = [
            'site-url' => get_bloginfo('url'),
            'site-wpurl' => get_bloginfo('wpurl'),
            'site-description' => get_bloginfo('description'),
            'site-rss_url' => get_bloginfo('rss_url'),
            'site-rss2_url' => get_bloginfo('rss2_url'),
            'site-atom_url' => get_bloginfo('atom_url'),
            'site-comments_atom_url' => get_bloginfo('comments_atom_url'),
            'site-comments_rss2_url' => get_bloginfo('comments_rss2_url'),
            'site-pingback_url' => get_bloginfo('pingback_url'),
            'site-stylesheet_url' => get_bloginfo('stylesheet_url'),
            'site-stylesheet_directory' => get_bloginfo('stylesheet_directory'),
            'site-template_directory' => get_bloginfo('template_directory'),
            'site-admin_email' => get_bloginfo('admin_email'),
            'site-charset' => get_bloginfo('charset'),
            'site-html_type' => get_bloginfo('html_type'),
            'site-version' => get_bloginfo('version'),
            'site-language' => get_bloginfo('language'),
            'site-text_direction' => get_bloginfo('text_direction'),
            'site-name' => get_bloginfo('name'),
        ];

        $this->mergeToTheData($data);
        return $this;
    }

    /**
     * @param callable|null $anotherElse
     * @param bool $withoutWp
     * @return CustomModel
     */
    public function addPostData(callable $anotherElse = null, $withoutWp = false)
    {
        /**
         * @var WP_Post $post
         */
        global $post;

        $queryObject = $this->getCurrentQueryObject();

        $data = [];
        if ($queryObject->have_posts()) {
            $queryObject->the_post();
            $data['title'] = get_the_title();

            ob_start();
            the_content();
            $data['content'] = ob_get_clean();

            $postData['excerpt'] = $post->post_excerpt;

            $data = array_merge($this->getAcfFromPage($post->ID), $data);
            if (!is_null($anotherElse)) {
                $anotherElse($data, $post);
            }
        }

        if(!$withoutWp) {
            ob_start();
            wp_head();
            $data['wp-head'] = ob_get_clean();

            ob_start();
            wp_footer();
            $data['wp-footer'] = ob_get_clean();
        }

        $queryObject->rewind_posts();
        wp_reset_postdata();

        $this->mergeToTheData($data);
        return $this;
    }

    /**
     * @param callable|null $anotherElse
     * @param bool $withoutWp
     * @return CustomModel
     */
    public function addPostLoopData(callable $anotherElse = null, $withoutWp = false)
    {
        /**
         * @var WP_Post $post
         */
        global $post;

        $queryObject = $this->getCurrentQueryObject();

        $data = [];
        while ($queryObject->have_posts()) {
            $queryObject->the_post();
            $postData = [];
            $postData['title'] = get_the_title();

            ob_start();
            the_content();
            $postData['content'] = ob_get_clean();

            $postData['excerpt'] = $post->post_excerpt;

            $postData = array_merge($this->getAcfFromPage($post->ID), $postData);

            $data['posts'][] = $postData;

            if (!is_null($anotherElse)) {
                $anotherElse($data['posts'][count($data['posts']) - 1], $post);
            }
        }

        if(is_object($queryObject->queried_object)) {
            $data['title'] = $queryObject->queried_object->name;
        }

        if(!$withoutWp) {
            ob_start();
            wp_head();
            $data['wp-head'] = ob_get_clean();

            ob_start();
            wp_footer();
            $data['wp-footer'] = ob_get_clean();
        }

        $queryObject->rewind_posts();
        wp_reset_postdata();

        $this->mergeToTheData($data);
        return $this;
    }

    /**
     * Add menus to template
     * @return $this
     */
    public function addMenu()
    {
        $getCurrentPage = function () {
            static $result = null;
            if (empty($result)) {
                global $wp;
                $result = home_url(add_query_arg([], $wp->request));
            }
            return $result;
        };

        $menuLocations = get_nav_menu_locations();
        $data = [];

        foreach ($menuLocations as $menuLocation => $menuId) {
            $menu = wp_get_nav_menu_items($menuId);

            foreach ($menu as $menuItem) {
                $item = new stdClass();

                /**
                 * @var WP_Post $menuItem
                 */
                $item->item = $menuItem->title;
                $item->link = $menuItem->url;
                $item->active = ($getCurrentPage === $menuItem->url) ? true : false;
                $item->special = ('online' === strtolower($menuItem->title)) ? true : false;

                $data[$menuLocation][] = $item;
            }
        }

        $this->mergeToTheData($data);
        return $this;
    }

    public function addVariables($data = [])
    {
        if (!is_array($data)) throw new \InvalidArgumentException("Wrong data to add to the template!");

        $this->mergeToTheData($data);
        return $this;
    }

    public function addAcfFromPage($pageId)
    {
        $this->mergeToTheData($this->getAcfFromPage($pageId));
        return $this;
    }

    public function addHomePageAcf()
    {
        $homePageId = (int)get_option('page_on_front');
        $this->addAcfFromPage($homePageId);
        return $this;
    }

    public function modifyValues($modifiers, $subKey = null)
    {
        $process = function(&$source) use ($modifiers) {
            foreach ($modifiers as $key => $value) {
                if (is_callable($value)) {
                    $source[$key] = $value((isset($source[$key]) ? $source[$key] : null));
                } else {
                    $source[$key] = $value;
                }
            }
        };

        if (is_array($modifiers)) {

            if(is_null($subKey) || !isset($this->data[$subKey])) {
                $process($this->data);
            } else {

                if(is_array($this->data[$subKey])) {
                    foreach ($this->data[$subKey] as $key => $data) {
                        $process($this->data[$subKey][$key]);
                    }
                } else {
                    $process($this->data[$subKey]);
                }
            }
        }

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $queryObject = $this->getCurrentQueryObject();
        return $queryObject->$name;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $queryObject = $this->getCurrentQueryObject();
        $queryObject->$name = $value;
    }

    /**
     * @param callable $name
     * @param array $arguments
     * @return false|mixed
     */
    public function __call($name, $arguments)
    {
        $queryObject = $this->getCurrentQueryObject();
        return $queryObject->__call($name, $arguments);
    }

    protected function getAcfFromPage($pageId)
    {
        if (!function_exists('get_fields')) return [];

        $data = get_fields($pageId);
        if (!is_array($data) || empty($data)) return [];

        return $data;
    }

    /**
     * @return WP_Query
     */
    protected function getCurrentQueryObject()
    {
        /**
         * @var WP_Query $wp_query
         */
        global $wp_query;

        /**
         * @var WP_Query $queryObject
         */
        $queryObject = $this->wpQuery;
        if (is_null($queryObject)) {
            wp_reset_query();
            $queryObject = $wp_query;
        }

        return $queryObject;
    }

    /**
     * Merge new values to the data
     * @param $newData
     */
    protected function mergeToTheData($newData)
    {
        if (!is_array($newData)) throw new \InvalidArgumentException("Wrong data to merge");
        $this->data = array_merge($this->data, $newData);
    }
}