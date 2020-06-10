<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\Widget;

class PetzkaDemoBackendWidget extends Contao\Widget
{
    /**
     * @var bool
     */
    protected $blnSubmitInput = true;

    /**
     * @var string
     */
    protected $strTemplate = 'be_widget';

    /**
     * @param mixed $varInput
     * @return mixed
     */
    protected function validator($varInput)
    {
        return parent::validator($varInput);
    }

    /**
     * @return string
     */
    public function generate()
    {

        // Textfeld
        $field = sprintf(

           '<input type="text" name="%s" id="ctrl_%s" value="%s">',
            $this->strName,
            $this->strId,
            $this->varValue

        );

       return  $field;

    }
}
