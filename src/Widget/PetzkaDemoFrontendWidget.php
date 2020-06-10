<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\Widget;

class PetzkaDemoFrontendWidget extends \Contao\FormSelectMenu
{
    /**
     * Parse the template file and return it as string
     * @param array $arrAttributes An optional attributes array
     * @return string The template markup
     */
    public function parse($arrAttributes=null)
    {
        if ($this->demo_value) {
            $this->is_demo = true;
        }

        // Widget erstellen
        return parent::parse($arrAttributes);
    }
}
