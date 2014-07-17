<?php
namespace Ilios\CoreBundle\Tests;

use IC\Bundle\Base\TestBundle\Test\BundleTestCase;
use Ilios\CoreBundle\IliosCoreBundle;

class IliosCoreBundleTest extends BundleTestCase
{
    public function testBuild()
    {
        $bundle = new IliosCoreBundle();

        $bundle->build($this->container);
    }
}
