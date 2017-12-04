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

use Mustache_Exception_UnknownTemplateException;
use Mustache_Loader;
use Mustache_Loader_FilesystemLoader;

class MustacheBlocksLoader extends Mustache_Loader_FilesystemLoader implements Mustache_Loader
{
    protected function loadFile($name)
    {
        $fileName = $this->getFileName($name);
        $fileName = str_replace(['/../', '/./'], '/blocks/', $fileName);
        if ($this->shouldCheckPath() && !file_exists($fileName)) {
            throw new Mustache_Exception_UnknownTemplateException($name);
        }

        return file_get_contents($fileName);
    }
}