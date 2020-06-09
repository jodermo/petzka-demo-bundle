<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\ContentElement;

use Contao\ContentElement;

class PetzkaDemoContentElement extends \ContentModule
{
  protected $strTemplate = 'demo_element';

  public function generate() {
    return parent::generate();
  }

  public function compile() {
    $this->Template->dummy = '<pre>Service: '.print_r($this->getContainer()->get('petzka.demobundle.demoservice')->getResult(),1).'</pre>';
  }

}
