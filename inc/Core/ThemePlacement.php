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
use CometbyTheme\Core\WidgetController;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ThemePlacement
 * @version   1.0
 * @package   CometbyTheme/Core
 * @category  Class
 * @author    Comet.by
 */
final class ThemePlacement
{
    use SingletonTrait;

    private $aPlacePropConf;

    /**
     * Procedure who set PlaceProperties array
     * @param array $aConf
     */
    public function setPlacePropConf($aConf = [])
    {
        if (!empty($aConf) && is_array($aConf)) {
            foreach ($aConf as $prop => $val) {
                $prop = (string)$prop;
                $this->aPlacePropConf[$prop] = $val;
            }
        }
    }

    /**
     * Print b-breadcrumbs block
     */
    public function theBreadcrumbs()
    {
        /* === ОПЦИИ === */
        $text['home'] = 'Главная'; // текст ссылки "Главная"
        $text['category'] = '%s'; // текст для страницы рубрики
        $text['search'] = 'Результаты поиска по запросу "%s"'; // текст для страницы с результатами поиска
        $text['tag'] = 'Записи с тегом "%s"'; // текст для страницы тега
        $text['author'] = 'Статьи автора %s'; // текст для страницы автора
        $text['404'] = 'Ошибка 404'; // текст для страницы 404
        $text['page'] = 'Страница %s'; // текст 'Страница N'
        $text['cpage'] = 'Страница комментариев %s'; // текст 'Страница комментариев N'

        $wrap_before = '<ul>'; // открывающий тег обертки
        $wrap_after = '</ul>'; // закрывающий тег обертки
        $show_home_link = 1; // 1 - показывать ссылку "Главная", 0 - не показывать
        $show_on_home = 0; // 1 - показывать "хлебные крошки" на главной странице, 0 - не показывать
        $show_current = 1; // 1 - показывать название текущей страницы, 0 - не показывать
        $before = '<li>'; // тег перед текущей "крошкой"
        $after = '</li>'; // тег после текущей "крошки"
        /* === КОНЕЦ ОПЦИЙ === */

        global $post;
        $home_link = home_url('/');
        $link_in_before = '';
        $link_in_after = '';
        $link_before = '<li>';
        $link_attr = '';
        $sep_before = '';
        $sep = '';
        $sep_after = '';
        $link_after = '</li> / ';
        $link = $link_before . '<a href="%1$s"' . $link_attr . '>' . $link_in_before . '%2$s' . $link_in_after . '</a>' . $link_after;
        $frontpage_id = get_option('page_on_front');
        $parent_id = $post->post_parent;
        $sep = ' ' . $sep_before . $sep . $sep_after . ' ';

        if (is_home() || is_front_page()) {

            if ($show_on_home) echo $wrap_before . '<a href="' . $home_link . '">' . $text['home'] . '</a>' . $wrap_after;

        } else {

            echo $wrap_before;
            if ($show_home_link) echo sprintf($link, $home_link, $text['home']);

            if (is_category()) {
                $cat = get_category(get_query_var('cat'), false);
                if ($cat->parent != 0) {
                    $cats = get_category_parents($cat->parent, TRUE, $sep);
                    $cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
                    $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr . '>' . $link_in_before . '$2' . $link_in_after . '</a>' . $link_after, $cats);
                    if ($show_home_link) echo $sep;
                    echo $cats;
                }
                if (get_query_var('paged')) {
                    $cat = $cat->cat_ID;
                    echo $sep . sprintf($link, get_category_link($cat), get_cat_name($cat)) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
                } else {
                    if ($show_current) echo $sep . $before . sprintf($text['category'], single_cat_title('', false)) . $after;
                }

            } elseif (is_search()) {
                if (have_posts()) {
                    if ($show_home_link && $show_current) echo $sep;
                    if ($show_current) echo $before . sprintf($text['search'], get_search_query()) . $after;
                } else {
                    if ($show_home_link) echo $sep;
                    echo $before . sprintf($text['search'], get_search_query()) . $after;
                }

            } elseif (is_day()) {
                if ($show_home_link) echo $sep;
                echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $sep;
                echo sprintf($link, get_month_link(get_the_time('Y'), get_the_time('m')), get_the_time('F'));
                if ($show_current) echo $sep . $before . get_the_time('d') . $after;

            } elseif (is_month()) {
                if ($show_home_link) echo $sep;
                echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y'));
                if ($show_current) echo $sep . $before . get_the_time('F') . $after;

            } elseif (is_year()) {
                if ($show_home_link && $show_current) echo $sep;
                if ($show_current) echo $before . get_the_time('Y') . $after;

            } elseif (is_single() && !is_attachment()) {
                if ($show_home_link) echo $sep;
                if (get_post_type() != 'post') {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;
                    printf($link, $home_link . '/' . $slug['slug'] . '/', $post_type->labels->singular_name);
                    if ($show_current) echo $sep . $before . get_the_title() . $after;
                } else {
                    $cat = get_the_category();
                    $cat = $cat[0];
                    $cats = get_category_parents($cat, TRUE, $sep);
                    if (!$show_current || get_query_var('cpage')) $cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
                    $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr . '>' . $link_in_before . '$2' . $link_in_after . '</a>' . $link_after, $cats);
                    echo $cats;
                    if (get_query_var('cpage')) {
                        echo $sep . sprintf($link, get_permalink(), get_the_title()) . $sep . $before . sprintf($text['cpage'], get_query_var('cpage')) . $after;
                    } else {
                        if ($show_current) echo $before . get_the_title() . $after;
                    }
                }

                // custom post type
            } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
                $post_type = get_post_type_object(get_post_type());
                if (get_query_var('paged')) {
                    echo $sep . sprintf($link, get_post_type_archive_link($post_type->name), $post_type->label) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
                } else {
                    if ($show_current) echo $sep . $before . $post_type->label . $after;
                }

            } elseif (is_attachment()) {
                if ($show_home_link) echo $sep;
                $parent = get_post($parent_id);
                $cat = get_the_category($parent->ID);
                $cat = $cat[0];
                if ($cat) {
                    $cats = get_category_parents($cat, TRUE, $sep);
                    $cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr . '>' . $link_in_before . '$2' . $link_in_after . '</a>' . $link_after, $cats);
                    echo $cats;
                }
                printf($link, get_permalink($parent), $parent->post_title);
                if ($show_current) echo $sep . $before . get_the_title() . $after;

            } elseif (is_page() && !$parent_id) {
                if ($show_current) echo $sep . $before . get_the_title() . $after;

            } elseif (is_page() && $parent_id) {
                if ($show_home_link) echo $sep;
                if ($parent_id != $frontpage_id) {
                    $breadcrumbs = array();
                    while ($parent_id) {
                        $page = get_page($parent_id);
                        if ($parent_id != $frontpage_id) {
                            $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
                        }
                        $parent_id = $page->post_parent;
                    }
                    $breadcrumbs = array_reverse($breadcrumbs);
                    for ($i = 0; $i < count($breadcrumbs); $i++) {
                        echo $breadcrumbs[$i];
                        if ($i != count($breadcrumbs) - 1) echo $sep;
                    }
                }
                if ($show_current) echo $sep . $before . get_the_title() . $after;

            } elseif (is_tag()) {
                if (get_query_var('paged')) {
                    $tag_id = get_queried_object_id();
                    $tag = get_tag($tag_id);
                    echo $sep . sprintf($link, get_tag_link($tag_id), $tag->name) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
                } else {
                    if ($show_current) echo $sep . $before . sprintf($text['tag'], single_tag_title('', false)) . $after;
                }

            } elseif (is_author()) {
                global $author;
                $author = get_userdata($author);
                if (get_query_var('paged')) {
                    if ($show_home_link) echo $sep;
                    echo sprintf($link, get_author_posts_url($author->ID), $author->display_name) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
                } else {
                    if ($show_home_link && $show_current) echo $sep;
                    if ($show_current) echo $before . sprintf($text['author'], $author->display_name) . $after;
                }

            } elseif (is_404()) {
                if ($show_home_link && $show_current) echo $sep;
                if ($show_current) echo $before . $text['404'] . $after;

            } elseif (has_post_format() && !is_singular()) {
                if ($show_home_link) echo $sep;
                echo get_post_format_string(get_post_format());
            }

            echo $wrap_after;

        }
    }

    /**
     * Print pagination block's content
     * @param int|'' $pages : max count of pages in the context
     * @param int $range : max range pagintaion buttons for both side
     */
    public function thePagination($pages = '', $range = 3)
    {
        if (!is_int($range) || ($range < 1)) $range = 3;
        $showitems = ($range * 2) + 1;

        global $paged;
        if (empty($paged)) $paged = 1;

        if (!is_int($pages) || ($pages < 1)) {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if (!$pages) {
                $pages = 1;
            }
        }

        if (1 != $pages) {
            echo '<ul class="afterclear"><li>Страницы:</li>';
            if ($paged > 2 && $paged > $range + 1 && $showitems < $pages) echo '<li class="first"><a href="' . get_pagenum_link(1) . '">&#xF100;</a></li>';
            if ($paged > 1 && $showitems < $pages) echo '<li class="prev"><a href="' . get_pagenum_link($paged - 1) . '">&#xF0D9;</a></li>';

            for ($i = 1; $i <= $pages; $i++) {
                if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems)) {
                    echo ($paged == $i) ? '<li class="active"><span>' . $i . '</span></li>' : '<li><a href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
                }
            }

            if ($paged < $pages && $showitems < $pages) echo '<li class="next"><a href="' . get_pagenum_link($paged + 1) . '">&#xF0DA;</a></li>';
            if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages) echo '<li class="last"><a href="' . get_pagenum_link($pages) . '">&#xF101;</a></li>';
            echo "</ul>\n";
        }
    }


    /**
     * @return null
     */
    public function singlePagination()
    {
        if (!empty(get_previous_post(true))) {
            ?>
            <div
                    class="b-arrow _type_prev"><?php previous_post_link('%link', '&#8592; Предыдущая статья', true); ?></a></div>
            <?php
        }
        if (!empty(get_next_post(true))) {
            ?>
            <div
                    class="b-arrow _type_next"><?php next_post_link('%link', 'Следующая статья &#8594;', true); ?></a></div>
            <?php
        }
        return null;
    }

    /**
     * @return array : for example [
     *   'key'    => 'home|category|page|single'|false,
     *   'id'     => int
     * ]
     */
    public function getWpPlacement()
    {
        $aRes = [
            'key' => false,
            'id' => 0,
            'comp' => false,
        ];

        /* key, id */
        global $post;
        if (is_front_page() || is_home()) {
            $aRes['key'] = 'home';

            if (get_option('show_on_front') == 'page') {
                $aRes['id'] = get_option('page_on_front');
            } else {
                $aRes['id'] = get_option('page_for_posts');
            }
        } elseif (is_category()) {
            $aRes['key'] = 'category';
            $aRes['id'] = get_query_var('cat');
        } elseif (is_page()) {
            $aRes['key'] = 'page';
            $aRes['id'] = $post->ID;
        } elseif (is_single()) {
            $aRes['key'] = 'single';
            $aRes['id'] = $post->ID;
        } elseif (is_404()) {
            $aRes['key'] = '404';
            $aRes['id'] = 0;
        } elseif (is_search()) {
            $aRes['key'] = 'search';
            $aRes['id'] = 0;
        }

        return $aRes;
    }

    /**
     * @return int : current category Id
     */
    public function getCatId()
    {
        return get_query_var('cat');
    }

    /**
     * @return string : current category name
     */
    public function getCatName()
    {
        $iCurCatId = get_query_var('cat');
        return get_cat_name($iCurCatId);
    }


    /**
     * @param bool $id
     * @return string the slug of the current category
     */
    public function getCurrCatSlug($id = false)
    {
        $cats = get_the_category($id);
        return $cats[0]->slug;
    }

    /**
     * @param string $propName
     * @return null|string
     */
    public function getPlaceProp($propName = '')
    {
        if (empty($propName)) return null;
        $propName = (string)$propName;

        $res = null;
        if (isset($this->aPlacePropConf[$propName])) $res = $this->aPlacePropConf[$propName];
        return $res;
    }

    public function theSidebar()
    {
        WidgetController::getInstance()->drawSideBar();
    }
}