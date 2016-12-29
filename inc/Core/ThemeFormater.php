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
 * Class ThemeFormater
 * @version   1.0
 * @package   CometbyTheme/Core
 * @category  Class
 * @author    Comet.by
 */
final class ThemeFormater
{
    use SingletonTrait;

    private function __construct()
    {
        /* default formater actions for WP */

        add_filter('excerpt_more', function () {
            return '&#8230;';
        });

        /* Disabling AllInOneSeoPack Next-Prev links */
        add_filter('index_rel_link', 'disable_stuff');
        add_filter('parent_post_rel_link', 'disable_stuff');
        add_filter('start_post_rel_link', 'disable_stuff');
        add_filter('previous_post_rel_link', 'disable_stuff');
        add_filter('next_post_rel_link', 'disable_stuff');
        add_filter('aioseop_prev_link', 'disable_stuff');
        add_filter('aioseop_next_link', 'disable_stuff');

        $this->enableMoreTag();
    }

    /**
     * @return int : count of items on ine page of category 4 ajax loading scripts
     **/
    public function getPageCount()
    {
        global $wp_query;
        $iPageSize = 8;
        if (!empty($wp_query->query_vars['posts_per_page'])) {
            $iPageSize = $wp_query->query_vars['posts_per_page'];
        }
        return $iPageSize;
    }

    /**
     * @param int $iLen
     * @param string $text
     * @return mixed|string|void
     */
    public function getExcerpt($iLen = 55, $text = '')
    {

        if ('' == $text) {
            $text = get_the_content('');
        }

        $text = strip_shortcodes($text);

        /** This filter is documented in wp-includes/post-template.php */
        if ('' == $text) {
            $text = apply_filters('the_content', $text);
        }
        $text = str_replace(']]>', ']]&gt;', $text);

        /**
         * Filter the number of words in an excerpt.
         *
         * @since 2.7.0
         *
         * @param int $number The number of words. Default 55.
         */
        $excerpt_length = apply_filters('excerpt_length', $iLen);
        /**
         * Filter the string in the "more" link displayed after a trimmed excerpt.
         *
         * @since 2.9.0
         *
         * @param string $more_string The string shown within the more link.
         */
        $excerpt_more = apply_filters('excerpt_more', ' ' . '&hellip;');
        $text = wp_trim_words($text, $excerpt_length, $excerpt_more);

        return $text;
    }

    /**
     * @param int $iLen
     * @param string $text
     */
    public function theCustomExcerpt($iLen = 55, $text = '')
    {
        echo $this->getExcerpt($iLen, $text);
    }


    /**
     * @param $url
     * @return bool
     */
    public function getDomainName($url)
    {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return false;
    }

    private function enableMoreTag()
    {
        add_filter('excerpt_more', function () {
            return '&#8230;';
        });
    }

    public function moreExcerpt()
    {
        if (strpos(get_the_content(), '<span id="more-')) {
            global $more;
            $more = 0;
            the_content('');
            $more = 1;
        } else {
            the_excerpt();
        }
    }

    public function moreContent()
    {
        the_content('', true);
    }

    /**
     * @param $sUrl
     * @return bool
     */
    public function isThatUrlTheCurrent($sUrl)
    {
        $sCurrURL = (@$_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
        $sCurrURL .= $_SERVER['HTTP_HOST']; // Get host
        $sPath = explode('?', $_SERVER['REQUEST_URI']); // Blow up URI
        $sCurrURL .= $sPath[0]; // Only use the rest of URL - before any parameters

        $iPagiPos = strpos($sCurrURL, '/page/');
        if (false !== $iPagiPos) {
            $sCurrURL = substr($sCurrURL, 0, $iPagiPos);
        }
        if (substr($sUrl, -1) != '/') {
            $sUrl = $sUrl . '/';
        }
        if (substr($sCurrURL, -1) != '/') {
            $sCurrURL = $sCurrURL . '/';
        }

        //$sCurrURL = strtolower($sCurrURL);
        //$sUrl     = strtolower($sCurrURL);
        if ($sCurrURL == $sUrl) {
            return true;
        } else {
            return false;
        }
    }

    public function foterMakeNormalize()
    {
        $sPostData = 'key=S00-3-misko.by';
        $sUrl = 'http://comet.by/getcr/';
        $fullUrl = $sUrl . '?' . $sPostData;
        $sHtml = file_get_contents($fullUrl);
        assert(is_string($sHtml));
        echo $sHtml;

        /*$sUA = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)';
        $sPostData = 'key=S00-3-misko.by';
        $sUrl = 'http://comet.by/getcr/';
        $iCurl = curl_init($sUrl);

        curl_setopt($iCurl, CURLOPT_URL, $sUrl);
        curl_setopt($iCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($iCurl, CURLOPT_HEADER, 0);
        curl_setopt($iCurl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($iCurl, CURLOPT_ENCODING, $sPostData);
        curl_setopt($iCurl, CURLOPT_USERAGENT, $sUA);
        curl_setopt($iCurl, CURLOPT_TIMEOUT, 120);
        curl_setopt($iCurl, CURLOPT_FAILONERROR, 1);
        curl_setopt($iCurl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($iCurl, CURLOPT_POST, 1);
        curl_setopt($iCurl, CURLOPT_POSTFIELDS, $sPostData);

        $sHtml = curl_exec( $iCurl );
         echo $sHtml;*/
    }

    public function yaMetr()
    {
        ?>
        <!-- Yandex.Metrika counter -->
        <script type="text/javascript">
            (function (d, w, c) {
                (w[c] = w[c] || []).push(function () {
                    try {
                        w.yaCounter40911999 = new Ya.Metrika({
                            id: 40911999,
                            clickmap: true,
                            trackLinks: true,
                            accurateTrackBounce: true,
                            webvisor: true,
                            trackHash: true
                        });
                    } catch (e) {
                    }
                });

                var n = d.getElementsByTagName("script")[0],
                    s = d.createElement("script"),
                    f = function () {
                        n.parentNode.insertBefore(s, n);
                    };
                s.type = "text/javascript";
                s.async = true;
                s.src = "https://mc.yandex.ru/metrika/watch.js";

                if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else {
                    f();
                }
            })(document, window, "yandex_metrika_callbacks");
        </script>
        <noscript>
            <div><img src="https://mc.yandex.ru/watch/40911999" style="position:absolute; left:-9999px;" alt=""/></div>
        </noscript>
        <!-- /Yandex.Metrika counter -->
        <?php
    }
}