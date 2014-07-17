<?php

namespace Ilios\LegacyCIBundle\Tests;

use Ilios\LegacyCIBundle\Utilities;

class UtilitiesTest extends TestCase
{

    /**
     * @var Utilities
     */
    protected $util;

    
    protected function setUp()
    {
        $this->util = new Utilities();
    }

    
    public function testSerializerArray()
    {
        $faker = \Faker\Factory::create();
        $data = array();
        for ($i = 0; $i < 25; $i++) {
            $data[$faker->text] = $faker->text;
        }
        $serialized = $this->util->serialize($data);
        foreach ($data as $key => $value) {
            $this->isFalse(strpos($serialized, $key) !== false);
            $this->isFalse(strpos($serialized, $value) !== false);
        }
        $unserialized = $this->util->unserialize($serialized);
        $this->assertSame($data, $unserialized);
    }
    
    public function testSerializerString()
    {
        $faker = \Faker\Factory::create();
        $data = $faker->text;
        $serialized = $this->util->serialize($data);
        $this->isFalse(strpos($serialized, $data) !== false);
        $unserialized = $this->util->unserialize($serialized);
        $this->assertSame($data, $unserialized);
    }
    
    public function testEncryption()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped(
                'The Mcrypt extension is not available.'
            );
        }
        $faker = \Faker\Factory::create();
        $data = $faker->text;
        $key = $faker->text(32);
        $encrypted = $this->util->encrypt($data, $key);
        $this->isTrue(strpos($encrypted, $data) !== false);
        $unencrypted = $this->util->decrypt($encrypted, $key);
        $this->assertSame($data, $unencrypted);
    }
}
