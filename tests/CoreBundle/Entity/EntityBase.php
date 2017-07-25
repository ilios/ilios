<?php

namespace Tests\CoreBundle\Entity;

use Faker\Factory;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection as Collection;
use Symfony\Component\Validator\Validation;
use Tests\CoreBundle\TestCase;

/**
 * Class EntityBase
 */
class EntityBase extends TestCase
{

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * Engage the symfony validator and test the object.
     * @param  integer $expectedCount how many errors are you expecting
     * @return array an abbreviated set of errors
     */
    protected function validate($expectedCount)
    {
        $validator = Validation::createValidatorBuilder()
                ->enableAnnotationMapping()
                ->getValidator();
        $errors = $validator->validate($this->object);
        $errorCount = count($errors);
        $parsedErrors = array();
        foreach ($errors as $error) {
            $constraintClass = get_class($error->getConstraint());
            //remove the namespace info
            $arr = explode('\\', $constraintClass);
            $parsedErrors[$error->getPropertyPath()] = array_pop($arr);
        }
        $this->assertEquals(
            $errorCount,
            $expectedCount,
            "Expected {$expectedCount} errors, found {$errorCount}: " .
            var_export($parsedErrors, true)
        );

        return $parsedErrors;
    }

    /**
     * @param array $fields
     */
    protected function validateNotBlanks(array $fields)
    {

        $errors = $this->validate(count($fields));

        foreach ($fields as $key) {
            $this->assertTrue(
                array_key_exists($key, $errors),
                "{$key} key not found in errors: " . var_export(array_keys($errors), true)
            );
            $this->assertSame('NotBlank', $errors[$key]);
        }
    }

    /**
     * @param array $fields
     */
    protected function validateNotNulls(array $fields)
    {

        $errors = $this->validate(count($fields));

        foreach ($fields as $key) {
            $this->assertTrue(
                array_key_exists($key, $errors),
                "{$key} key not found in errors: " . var_export(array_keys($errors), true)
            );
            $this->assertSame('NotNull', $errors[$key]);
        }
    }

    /**
     * A generic test for entity setters.
     *
     * @param string $property
     * @param string $type
     */
    protected function basicSetTest($property, $type)
    {
        $setMethod = $this->getSetMethodForProperty($property);
        $getMethod = $this->getGetMethodForProperty($property);
        $this->assertTrue(method_exists($this->object, $setMethod), "Method {$setMethod} missing");
        $this->assertTrue(method_exists($this->object, $getMethod), "Method {$getMethod} missing");
        $expected = $this->getValueForType($type);
        $this->object->$setMethod($expected);
        $this->assertSame($expected, $this->object->$getMethod());
    }

    /**
     * A generic test for boolean entity setters.
     *
     * @param string $property
     * @param boolean $is should we use is vs has when generating the method.
     */
    protected function booleanSetTest($property, $is = true)
    {
        $setMethod = $this->getSetMethodForProperty($property);
        $isMethod = $is?$this->getIsMethodForProperty($property):$this->getHasMethodForProperty($property);
        $this->assertTrue(method_exists($this->object, $setMethod), "Method {$setMethod} missing");
        $this->assertTrue(method_exists($this->object, $isMethod), "Method {$isMethod} missing");
        $expected = $this->getValueForType('boolean');
        $this->object->$setMethod($expected);
        $this->assertSame($expected, $this->object->$isMethod());
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
        $this->assertTrue(method_exists($this->object, $getMethod), "Method {$getMethod} missing");
        $this->assertTrue(method_exists($this->object, $setMethod), "Method {$setMethod} missing");
        $obj = m::mock('Ilios\CoreBundle\Entity\\' . $entityName);
        $this->object->$setMethod($obj);
        $this->assertSame($obj, $this->object->$getMethod());
    }

    /**
     * A generic test for setters for collections.
     * @todo should we mock Collection when passing it to the setMethod?
     *
     * @param string $property
     * @param string $entityName
     * @param string|bool $getter name of the method to use instead of a generated method, or FALSE if n/a.
     * @param string|bool $setter name of the method to use instead of a generated method, or FALSE if n/a.
     * @param string|bool $crossSaveMethod name of the method to call on the inverse side of the relationship.
     */
    protected function entityCollectionSetTest(
        $property,
        $entityName,
        $getter = false,
        $setter = false,
        $crossSaveMethod = false
    ) {
        $getMethod = $getter?$getter:$this->getGetMethodForCollectionProperty($property);
        $setMethod = $setter?$setter:$this->getSetMethodForCollectionProperty($property);
        $this->assertTrue(method_exists($this->object, $setMethod), "Method {$setMethod} missing");
        $this->assertTrue(method_exists($this->object, $getMethod), "Method {$getMethod} missing");
        $arr = $this->getArrayOfMockObjects('Ilios\CoreBundle\Entity\\' . $entityName, 10);
        if ($crossSaveMethod) {
            foreach ($arr as $obj) {
                $obj->shouldReceive($crossSaveMethod)->with($this->object)->once();
            }
        }
        $collection = new Collection($arr);
        $this->object->$setMethod($collection);
        $results = $this->object->$getMethod();
        $this->assertTrue($results instanceof Collection, 'Collection not returned.');

        foreach ($arr as $obj) {
            $this->assertTrue($results->contains($obj));
        }
    }


    /**
     * A generic test for entity setters which hold collections of other entities.
     *
     * @param string $property
     * @param string $entityName
     * @param string|bool $getter name of the method to use instead of a generated method, or FALSE if n/a.
     * @param string|bool $setter name of the method to use instead of a generated method, or FALSE if n/a.
     * @param string|bool $crossSaveMethod name of the method to call on the inverse side of the relationship.
     */
    protected function entityCollectionAddTest(
        $property,
        $entityName,
        $getter = false,
        $setter = false,
        $crossSaveMethod = false
    ) {
        $arr = $this->getArrayOfMockObjects('Ilios\CoreBundle\Entity\\' . $entityName, 10);
        $addMethod = $setter?$setter:$this->getAddMethodForProperty($property);
        $getMethod = $getter?$getter:$this->getGetMethodForCollectionProperty($property);
        $this->assertTrue(method_exists($this->object, $addMethod), "Method {$addMethod} missing");
        $this->assertTrue(method_exists($this->object, $getMethod), "Method {$getMethod} missing");
        foreach ($arr as $obj) {
            if ($crossSaveMethod) {
                $obj->shouldReceive($crossSaveMethod)->with($this->object)->once();
            }
            $this->object->$addMethod($obj);
        }
        $results = $this->object->$getMethod();
        $this->assertTrue($results instanceof Collection, 'Collection not returned.');

        foreach ($arr as $obj) {
            $this->assertTrue($results->contains($obj));
        }
    }

    /**
     * A generic test for entity setters which hold collections of other entities.
     *
     * @param string $property
     * @param string $entityName
     * @param string|bool $getter name of the method to use instead of a generated method, or FALSE if n/a.
     * @param string|bool $adder name of the method used to add instead of a generated method, or FALSE if n/a.
     * @param string|bool $remover name of the method to use instead of a generated method, or FALSE if n/a.
     * @param string|bool $crossSaveMethod name of the method to call on the inverse side of the relationship.
     */
    protected function entityCollectionRemoveTest(
        $property,
        $entityName,
        $getter = false,
        $adder = false,
        $remover = false,
        $crossSaveMethod = false
    ) {
        $arr = $this->getArrayOfMockObjects('Ilios\CoreBundle\Entity\\' . $entityName, 10);
        $addMethod = $adder?$adder:$this->getAddMethodForProperty($property);
        $removeMethod = $remover?$remover:$this->getRemoveMethodForProperty($property);
        $getMethod = $getter?$getter:$this->getGetMethodForCollectionProperty($property);
        $this->assertTrue(method_exists($this->object, $addMethod), "Method {$addMethod} missing from {$entityName}");
        $this->assertTrue(
            method_exists($this->object, $removeMethod),
            "Method {$removeMethod} missing from {$entityName}"
        );
        $this->assertTrue(method_exists($this->object, $getMethod), "Method {$getMethod} missing from {$entityName}");

        foreach ($arr as $obj) {
            $obj->shouldIgnoreMissing();
            $this->object->$addMethod($obj);
        }
        $results = $this->object->$getMethod();
        $this->assertTrue($results instanceof Collection, 'Collection not returned.');
        foreach ($arr as $obj) {
            if ($crossSaveMethod) {
                $obj->shouldReceive($crossSaveMethod)->with($this->object)->once();
            }
            $this->assertTrue($results->contains($obj));
        }

        foreach ($arr as $obj) {
            $this->object->$removeMethod($obj);
        }
        $results = $this->object->$getMethod();
        $this->assertTrue($results instanceof Collection, 'Collection not returned.');
        foreach ($arr as $obj) {
            $this->assertTrue(!$results->contains($obj), 'Entity was not removed correctly');
        }
    }

    /**
     * @param $className
     * @param $count
     * @return array
     */
    protected function getArrayOfMockObjects($className, $count)
    {
        $arr = array();
        for ($i = 0; $i < $count; $i++) {
            $arr[] = m::mock($className);
        }

        return $arr;
    }

    /**
     * @param $property
     * @return string
     */
    protected function getSetMethodForProperty($property)
    {
        return 'set' . ucfirst($property);
    }

    /**
     * @param $property
     * @return string
     */
    protected function getGetMethodForProperty($property)
    {
        return 'get' . ucfirst($property);
    }

    /**
     * @param $property
     * @return string
     */
    protected function getIsMethodForProperty($property)
    {
        return 'is' . ucfirst($property);
    }

    /**
     * @param $property
     * @return string
     */
    protected function getHasMethodForProperty($property)
    {
        return 'has' . ucfirst($property);
    }

    /**
     * @param $property
     * @return string
     */
    protected function getGetMethodForCollectionProperty($property)
    {
        return 'get' . ucfirst($property) . 's';
    }

    /**
     * @param $property
     * @return string
     */
    protected function getSetMethodForCollectionProperty($property)
    {
        return 'set' . ucfirst($property) . 's';
    }

    /**
     * @param $property
     * @return string
     */
    protected function getAddMethodForProperty($property)
    {
        return 'add' . ucfirst($property);
    }

    /**
     * @param $property
     * @return string
     */
    protected function getRemoveMethodForProperty($property)
    {
        return 'remove' . ucfirst($property);
    }

    /**
     * @param string $type
     * @return \DateTime|float|int|bool|string
     * @throws \Exception
     */
    protected function getValueForType($type)
    {
        $faker = Factory::create();
        switch ($type) {
            case 'integer':
                return $faker->randomNumber();
            case 'float':
                return $faker->randomFloat();
            case 'string':
                return $faker->text;
            case 'hexcolor':
                return $faker->hexColor;
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
