<?php

namespace Ilios\CoreBundle\Tests\Entity;

use IC\Bundle\Base\TestBundle\Test\TestCase;
use Mockery as m;

class EntityBase extends TestCase
{
    
    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * A generic test for entity getters
     * 
     * @param string $property
     * @param string $type
     */
    protected function basicGetTest($property, $type)
    {
        $expected = $this->getValueForType($type);
        $getMethod = $this->getGetMethodForProperty($property);
        $this->setPropertyOnObject($this->object, $property, $expected);
        $this->assertSame($expected, $this->object->$getMethod());
    }

    /**
     * A generic test for entity setters
     * 
     * @param string $property
     * @param string $type
     */
    protected function basicSetTest($property, $type)
    {
        $setMethod = $this->getSetMethodForProperty($property);
        $getMethod = $this->getGetMethodForProperty($property);
        $expected = $this->getValueForType($type);
        $this->object->$setMethod($expected);
        $this->assertSame($expected, $this->object->$getMethod());
    }

    /**
     * A generic test for entity getters which use other entites
     * 
     * @param string $property
     * @param string $entityName
     */
    protected function entityGetTest($property, $entityName)
    {
        $obj = m::mock('Ilios\CoreBundle\Entity\\' . $entityName);
        $getMethod = $this->getGetMethodForProperty($property);
        $this->setPropertyOnObject($this->object, $property, $obj);
        $this->assertSame($obj, $this->object->$getMethod());
    }

    /**
     * A generic test for entity setters which use other entites
     * 
     * @param string $property
     * @param string $entityName
     */
    protected function entitySetTest($property, $entityName)
    {
        $setMethod = $this->getSetMethodForProperty($property);
        $getMethod = $this->getGetMethodForProperty($property);
        $obj = m::mock('Ilios\CoreBundle\Entity\\' . $entityName);
        $this->object->$setMethod($obj);
        $this->assertSame($obj, $this->object->$getMethod());
    }

    /**
     * A generic test for getters which hold collections of other entites
     * 
     * @param string $property
     * @param string $entityName
     */
    protected function entityCollectionGetTest($property, $entityName, $getter = false, $pluralize = true)
    {
        $arr = $this->getArrayOfMockObjects('Ilios\CoreBundle\Entity\\' . $entityName, 10);
        $getMethod = $getter?$getter:$this->getGetMethodForCollectionProperty($property);
        $collection = m::mock(
            'Doctrine\Common\Collections\Collection',
            array('toArray' => $arr)
        );
        if ($pluralize) {
            $property .= 's';
        }
        $this->setPropertyOnObject($this->object, $property, $collection);
        $this->assertSame($arr, $this->object->$getMethod());
    }

    /**
     * A generis test for entity setters which hold collections of other entites
     * 
     * @param string $property
     * @param string $entityName
     */
    protected function entityCollectionAddTest($property, $entityName, $getter = false, $setter = false)
    {
        $arr = $this->getArrayOfMockObjects('Ilios\CoreBundle\Entity\\' . $entityName, 10);
        $addMethod = $setter?$setter:$this->getAddMethodForProperty($property);
        $getMethod = $getter?$getter:$this->getGetMethodForCollectionProperty($property);
        foreach ($arr as $obj) {
            $this->object->$addMethod($obj);
        }

        $this->assertSame($arr, $this->object->$getMethod());
    }

    /**
     * A generis test for entity setters which hold collections of other entites
     * 
     * @param string $property
     * @param string $entityName
     */
    protected function entityCollectionRemoveTest(
        $property,
        $entityName,
        $getter = false,
        $setter = false,
        $remover = false
    ) {
        $arr = $this->getArrayOfMockObjects('Ilios\CoreBundle\Entity\\' . $entityName, 5);
        $addMethod = $setter?$setter:$this->getAddMethodForProperty($property);
        $getMethod = $getter?$getter:$this->getGetMethodForCollectionProperty($property);
        $removeMethod = $remover?$remover:$this->getRemoveMethodForProperty($property);
        foreach ($arr as $obj) {
            $this->object->$addMethod($obj);
        }
        $key = array_rand($arr);
        $obj = $arr[$key];
        unset($arr[$key]);
        $this->object->$removeMethod($obj);
        $this->assertSame($arr, $this->object->$getMethod());
    }

    protected function getArrayOfMockObjects($className, $count)
    {
        $arr = array();
        for ($i = 0; $i < $count; $i++) {
            $arr[] = m::mock($className);
        }

        return $arr;
    }

    protected function getSetMethodForProperty($property)
    {
        return 'set' . ucfirst($property);
    }

    protected function getGetMethodForProperty($property)
    {
        return 'get' . ucfirst($property);
    }

    protected function getGetMethodForCOllectionProperty($property)
    {
        return 'get' . ucfirst($property) . 's';
    }

    protected function getAddMethodForProperty($property)
    {
        return 'add' . ucfirst($property);
    }

    protected function getRemoveMethodForProperty($property)
    {
        return 'remove' . ucfirst($property);
    }

    protected function getValueForType($type)
    {
        $faker = \Faker\Factory::create();
        switch ($type) {
            case 'integer':
                return $faker->randomNumber();
            case 'string':
                return $faker->text;
            case 'email':
                return $faker->email;
            case 'phone':
                return $faker->phoneNumber;
            case 'datetime':
                return $faker->dateTime;
            case 'bool':
            case 'boolean':
                return $faker->boolean;
            default:
                throw new \Exception("No values for type {$type}");
        }
    }
}
