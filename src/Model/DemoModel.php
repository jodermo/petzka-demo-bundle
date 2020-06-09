<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\Model;

use Contao\Database;
use Contao\Date;
use Contao\FilesModel;

class ParentModel extends \Contao\Model
{
}

class DemoModel extends ParentModel
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_demo';

    /**
     * Get the CSS class.
     *
     * @return string
     */
    public function getCssClass()
    {
        $cssClasses = [
            'demo_'.$this->id,
        ];

        if ($this->cssClass) {
            $cssClasses[] = $this->cssClass;
        }

        return \implode(' ', \array_unique($cssClasses));
    }

    /**
     * Get the image.
     *
     * @return FilesModel|null
     */
    public function getImage()
    {
        return $this->image ? FilesModel::findByPk($this->image) : null;
    }

    /**
     * Get the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->frontendTitle ?: $this->title;
    }


}
