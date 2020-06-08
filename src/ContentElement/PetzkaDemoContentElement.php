<?php

namespace Petzka\DemoBundle\ContentElement;

use Contao\ContentElement;

class PetzkaDemoContentElement extends \ContentModule
{
  protected $strTemplate = 'dummy_default';
  
  public function generate() {
    return parent::generate();
  }

  public function compile() {
    $this->Template->dummy = '<pre>Service: '.print_r($this->getContainer()->get('petzka.demobundle.demoservice')->getResult(),1).'</pre>';
  }

}
