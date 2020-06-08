<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle;

use Contao\System;

class MultilingualHelper
{
    /**
     * Return true if the multilingual features are active.
     *
     * @return bool
     */
    public static function isActive()
    {
        return \array_key_exists('Terminal42DcMultilingualBundle', System::getContainer()->getParameter('kernel.bundles'));
    }
}
