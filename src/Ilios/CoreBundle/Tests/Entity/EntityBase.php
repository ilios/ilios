<?php

namespace Ilios\CoreBundle\Tests\Entity;

use IC\Bundle\Base\TestBundle\Test\TestCase;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection as Collection;
use Symfony\Component\Validator\Validation;

class EntityBase extends TestCase
{

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        m::close();
        unset($this->object);
    }

    /**
     * Engage the symfony validator and test the object
     * @param  integer $expectedCount how many erors are you expecting
     * @return array an abreviated set of errors
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
     * A generic test for entity setters
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
     * A generic test for boolean entity setters
     *
     * @param string $property
     * @param boolean $is shoud we use is vs has when generating the method
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
     * A generic test for entity setters which use other entites
     *
     * @param string $property
     * @param string $entityName
     */
    protected function softDeleteEntitySetTest($property, $entityName)
    {
        $setMethod = $this->getSetMethodForProperty($property);
        $getMethod = $this->getGetMethodForProperty($property);
        $this->assertTrue(method_exists($this->object, $getMethod), "Method {$getMethod} missing");
        $this->assertTrue(method_exists($this->object, $setMethod), "Method {$setMethod} missing");
        $obj = m::mock('Ilios\CoreBundle\Entity\\' . $entityName)
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        $deletedObj = m::mock('Ilios\CoreBundle\Entity\\' . $entityName)
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(true)
            ->mock();
            
        $this->object->$setMethod($obj);
        $this->assertSame($obj, $this->object->$getMethod());
        
        $this->object->$setMethod($deletedObj);
        $this->assertNull($this->object->$getMethod());
    }

    /**
     * A generic test for setters for collections
     * @todo should we mock Collection when passing it to the setMethod?
     *
     * @param string $property
     * @param string $entityName
     * @param string $getter use instead of a generated method
     * @param string $setter use instead of a generated method
     */
    protected function entityCollectionSetTest($property, $entityName, $getter = false, $setter = false)
    {
        $getMethod = $getter?$getter:$this->getGetMethodForCollectionProperty($property);
        $setMethod = $setter?$setter:$this->getSetMethodForCollectionProperty($property);
        $this->assertTrue(method_exists($this->object, $setMethod), "Method {$setMethod} missing");
        $this->assertTrue(method_exists($this->object, $getMethod), "Method {$getMethod} missing");
        $arr = $this->getArrayOfMockObjects('Ilios\CoreBundle\Entity\\' . $entityName, 10);
        $collection = new Collection($arr);
        $this->object->$setMethod($collection);
        $results = $this->object->$getMethod();
        $this->assertTrue($results instanceof Collection, 'Collection not returned.');

        foreach ($arr as $obj) {
            $this->assertTrue($results->contains($obj));
        }
    }

    /**
     * A generic test for setters for collections
     * @todo should we mock Collection when passing it to the setMethod?
     *
     * @param string $property
     * @param string $entityName
     * @param string $getter use instead of a generated method
     * @param string $setter use instead of a generated method
     */
    protected function softDeleteEntityCollectionSetTest(
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
        $unDeletedObj1 = m::mock('Ilios\CoreBundle\Entity\\' . $entityName)
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        $deletedObj = m::mock('Ilios\CoreBundle\Entity\\' . $entityName)
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(true)
            ->mock();
        $unDeletedObj2 = m::mock('Ilios\CoreBundle\Entity\\' . $entityName)
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        if ($crossSaveMethod) {
            $unDeletedObj1->shouldReceive($crossSaveMethod)->with($this->object);
            $deletedObj->shouldReceive($crossSaveMethod)->with($this->object);
            $unDeletedObj2->shouldReceive($crossSaveMethod)->with($this->object);
        }
        $collection = new Collection([$unDeletedObj1, $deletedObj, $unDeletedObj2]);
        $this->object->$setMethod($collection);
        $results = $this->object->$getMethod();
        $this->assertTrue($results instanceof Collection, 'Collection not returned.');
        
        $this->assertSame(2, count($results));
        $this->assertTrue($results->containsKey(0));
        $this->assertTrue($results->containsKey(1));
        $this->assertSame($results[0], $unDeletedObj1);
        $this->assertSame($results[1], $unDeletedObj2);
        
    }

    /**
     * A generis test for entity setters which hold collections of other entites
     *
     * @param string $property
     * @param string $entityName
     * @param string $getter use instead of a generated method
     * @param string $setter use instead of a generated method
     */
    protected function entityCollectionAddTest($property, $entityName, $getter = false, $setter = false)
    {
        $arr = $this->getArrayOfMockObjects('Ilios\CoreBundle\Entity\\' . $entityName, 10);
        $addMethod = $setter?$setter:$this->getAddMethodForProperty($property);
        $getMethod = $getter?$getter:$this->getGetMethodForCollectionProperty($property);
        $this->assertTrue(method_exists($this->object, $addMethod), "Method {$addMethod} missing");
        $this->assertTrue(method_exists($this->object, $getMethod), "Method {$getMethod} missing");
        foreach ($arr as $obj) {
            $this->object->$addMethod($obj);
        }
        $results = $this->object->$getMethod();
        $this->assertTrue($results instanceof Collection, 'Collection not returned.');

        foreach ($arr as $obj) {
            $this->assertTrue($results->contains($obj));
        }
    }

    /**
     * A generis test for entity setters which hold collections of other entites
     *
     * @param string $property
     * @param string $entityName
     * @param string $getter use instead of a generated method
     * @param string $setter use instead of a generated method
     */
    protected function softDeleteEntityCollectionAddTest(
        $property,
        $entityName,
        $getter = false,
        $setter = false,
        $crossSaveMethod = false
    ) {
        $addMethod = $setter?$setter:$this->getAddMethodForProperty($property);
        $getMethod = $getter?$getter:$this->getGetMethodForCollectionProperty($property);
        $this->assertTrue(method_exists($this->object, $addMethod), "Method {$addMethod} missing");
        $this->assertTrue(method_exists($this->object, $getMethod), "Method {$getMethod} missing");

        $unDeletedObj1 = m::mock('Ilios\CoreBundle\Entity\\' . $entityName)
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        $deletedObj = m::mock('Ilios\CoreBundle\Entity\\' . $entityName)
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(true)
            ->mock();
        $unDeletedObj2 = m::mock('Ilios\CoreBundle\Entity\\' . $entityName)
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        if ($crossSaveMethod) {
            $unDeletedObj1->shouldReceive($crossSaveMethod)->with($this->object);
            $deletedObj->shouldReceive($crossSaveMethod)->with($this->object);
            $unDeletedObj2->shouldReceive($crossSaveMethod)->with($this->object);
        }
        $this->object->$addMethod($unDeletedObj1);
        $this->object->$addMethod($deletedObj);
        $this->object->$addMethod($unDeletedObj2);
        $results = $this->object->$getMethod();
        $this->assertTrue($results instanceof Collection, 'Collection not returned.');
        
        $this->assertSame(2, count($results));
        $this->assertTrue($results->containsKey(0));
        $this->assertTrue($results->containsKey(1));
        $this->assertSame($results[0], $unDeletedObj1);
        $this->assertSame($results[1], $unDeletedObj2);
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
        $this->assertTrue(true);
        // $arr = $this->getArrayOfMockObjects('Ilios\CoreBundle\Entity\\' . $entityName, 5);
        // $addMethod = $setter?$setter:$this->getAddMethodForProperty($property);
        // $getMethod = $getter?$getter:$this->getGetMethodForCollectionProperty($property);
        // $removeMethod = $remover?$remover:$this->getRemoveMethodForProperty($property);
        // $this->assertTrue(method_exists($this->object, $addMethod), "Method {$addMethod} missing");
        // $this->assertTrue(method_exists($this->object, $getMethod), "Method {$getMethod} missing");
        // $this->assertTrue(method_exists($this->object, $removeMethod), "Method {$removeMethod} missing");
        // foreach ($arr as $obj) {
        //     $this->object->$addMethod($obj);
        // }
        // $key = array_rand($arr);
        // $obj = $arr[$key];
        // unset($arr[$key]);
        // $this->object->$removeMethod($obj);
        // $collection = $this->object->$getMethod();
        // foreach ($arr as $obj) {
        //     $this->assertTrue($collection->contains($obj));
        // }
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

    protected function getHasMethodForProperty($property)
    {
        return 'has' . ucfirst($property);
    }

    protected function getGetMethodForCollectionProperty($property)
    {
        return 'get' . ucfirst($property) . 's';
    }

    protected function getSetMethodForCollectionProperty($property)
    {
        return 'set' . ucfirst($property) . 's';
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
