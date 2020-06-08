<?php

namespace Petzka\DemoBundle\Widget;


class PetzkaDemoModule extends \ContentModule
{
    /**
     * Parse the template.
     *
     * @return string
     */
    public function generate()
    {
        // Return if the element is not published
        if (TL_MODE === 'FE'
            && !BE_USER_LOGGED_IN
            && ($this->invisible || ($this->start > 0 && $this->start > \time()) || ($this->stop > 0 && $this->stop < \time()))
        ) {
            return '';
        }

        // Return if the module could not be found
        if (null === ($moduleModel = ModuleModel::findByPk($this->content_module))) {
            return '';
        }

        $class = Module::findClass($moduleModel->type);

        // Return if the class does not exist
        if (!\class_exists($class)) {
            return '';
        }

        $moduleModel->typePrefix = 'ce_';

        /** @var Module $module */
        $module = new $class($moduleModel, $this->strColumn);

        $this->mergeCssId($module);

        return $module->generate();
    }

    /**
     * Merge the CSS/ID stuff.
     *
     * @param Module $module
     */
    private function mergeCssId(Module $module)
    {
        $cssID = StringUtil::deserialize($module->cssID, true);

        // Override the CSS ID (see #305)
        if ($this->cssID[0]) {
            $cssID[0] = $this->cssID[0];
        }

        // Merge the CSS classes (see #6011)
        if ($this->cssID[1]) {
            $cssID[1] = \trim($cssID[1].' '.$this->cssID[1]);
        }

        $module->cssID = $cssID;
    }
}
