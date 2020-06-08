<?php

/*
 * This file is part of [petzka/demo-bundle].
 *
 * (c) Moritz Petzka
 *
 * @license LGPL-3.0-or-later
 */

namespace Petzka\DemoBundle\Tests;

use Petzka\DemoBundle\PetzkaDemoBundle;
use PHPUnit\Framework\TestCase;

class PetzkaDemoBundleTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $bundle = new ContaoSkeletonBundle();

        $this->assertInstanceOf('Petzka\DemoBundle\PetzkaDemoBundle', $bundle);
    }
}
