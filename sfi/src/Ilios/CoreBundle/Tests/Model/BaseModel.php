<?php

namespace Ilios\CoreBundle\Tests\Model;

use IC\Bundle\Base\TestBundle\Test\TestCase;
use Mockery as m;

class BaseModel extends TestCase
{
    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * A generic test for model getters
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
     * A generic test for model setters
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
     * A generic test for boolean entity setters
     *
     * @param string $property
     */
    protected function booleanSetTest($property)
    {
        $setMethod = $this->getSetMethodForProperty($property);
        $isMethod = $this->getIsMethodForProperty($property);
        $expected = $this->getValueForType('boolean');
        $this->object->$setMethod($expected);
        $this->assertSame($expected, $this->object->$isMethod());
    }

    /**
     * A generic test for entity getters
     *
     * @param string $property
     * @param string $type
     */
    protected function basicIsTest($property, $type)
    {
        $expected = $this->getValueForType($type);
        $isMethod = $this->getIsMethodForProperty($property);
        $this->setPropertyOnObject($this->object, $property, $expected);
        $this->assertSame($expected, $this->object->$isMethod());
    }

    /**
     * A generic test for entity getters
     *
     * @param string $property
     * @param string $type
     */
    protected function basicHasTest($property, $type)
    {
        $expected = $this->getValueForType($type);
        $isMethod = $this->getHasMethodForProperty($property);
        $this->setPropertyOnObject($this->object, $property, $expected);
        $this->assertSame($expected, $this->object->$isMethod());
    }

    /**
     * A generic test for model getters which use other entites
     *
     * @param string $property
     * @param string $modelName
     */
    protected function modelGetTest($property, $modelName)
    {
        $obj = m::mock('Ilios\CoreBundle\Model\\' . $modelName);
        $getMethod = $this->getGetMethodForProperty($property);
        $this->setPropertyOnObject($this->object, $property, $obj);
        $this->assertSame($obj, $this->object->$getMethod());
    }

    /**
     * A generic test for model setters which use other entites
     *
     * @param string $property
     * @param string $modelName
     */
    protected function modelSetTest($property, $modelName)
    {
        $setMethod = $this->getSetMethodForProperty($property);
        $getMethod = $this->getGetMethodForProperty($property);
        $obj = m::mock('Ilios\CoreBundle\Model\\' . $modelName);
        $this->object->$setMethod($obj);
        $this->assertSame($obj, $this->object->$getMethod());
    }

    /**
     * A generic test for getters which hold collections of other entites
     *
     * @param string $property
     * @param string $modelName
     */
    protected function modelCollectionGetTest($property, $modelName, $getter = false, $pluralize = true)
    {
        $arr = $this->getArrayOfMockObjects('Ilios\CoreBundle\Model\\' . $modelName, 10);
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
     * A generic test for model setters which hold collections of other entites
     *
     * @param string $property
     * @param string $modelName
     * @param bool $getter
     * @param bool $setter
     */
    protected function modelCollectionAddTest($property, $modelName, $getter = false, $setter = false)
    {
        $arr = $this->getArrayOfMockObjects('Ilios\CoreBundle\Model\\' . $modelName, 10);
        $addMethod = $setter?$setter:$this->getAddMethodForProperty($property);
        $getMethod = $getter?$getter:$this->getGetMethodForCollectionProperty($property);
        foreach ($arr as $obj) {
            $this->object->$addMethod($obj);
        }

        $this->assertSame($arr, $this->object->$getMethod());
    }

    /**
     * A generic test for model setters which hold collections of other entites
     *
     * @param string $property
     * @param string $modelName
     * @param bool $getter
     * @param bool $setter
     * @param bool $remover
     */
    protected function modelCollectionRemoveTest(
        $property,
        $modelName,
        $getter = false,
        $setter = false,
        $remover = false
    ) {
        $arr = $this->getArrayOfMockObjects('Ilios\CoreBundle\Model\\' . $modelName, 5);
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

    protected function getIsMethodForProperty($property)
    {
        return 'is' . ucfirst($property);
    }

    /*
     * @TODO: This may not always work...need to use an inflector.
     */
    protected function getGetMethodForCollectionProperty($property)
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
            case 'int':
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

        return false;
    }
}
