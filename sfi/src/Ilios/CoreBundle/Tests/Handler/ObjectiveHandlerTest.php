<?php

namespace Ilios\CoreBundle\Tests\Handler;

use Ilios\CoreBundle\Handler\ObjectiveHandler;

class ObjectiveHandlerTest extends \PHPUnit_Framework_TestCase
{
    const ENT_CLASS = 'Ilios\CoreBundle\Entity\Objective';

    /** @var ObjectiveHandler */
    protected $handler;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $om;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $repository;

    public function setUp()
    {
        if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }

        $class = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::ENT_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::ENT_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::ENT_CLASS));
    }

    public function testGet()
    {
        $id = 1;
        $objective = $this->getObjective();
        $this->repository->expects($this->once())->method('find')
            ->with($this->equalTo($id))
            ->will($this->returnValue($objective));

        $handler = $this->createHandler();

        $handler->get($id);
    }

    public function testGetAll()
    {
        $objectives = array(
            $this->getObjective()->setTitle('first'),
            $this->getObjective()->setTitle('second'),
            $this->getObjective()->setTitle('third')
        );
        $this->repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($objectives));

        $handler = $this->createHandler();

        $handler->getAll();
    }

    public function testPost()
    {
        $title = 'title1';

        $parameters = array('title' => $title);

        $objective = $this->getObjective();
        $objective->setTitle($title);

        $form = $this->getMock('Symfony\Component\Form\FormInterface');
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($objective));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $handler = $this->createHandler();
        $object = $handler->post($parameters);

        $this->assertEquals($object, $objective);
    }

    /**
     * @expectedException Ilios\CoreBundle\Exception\InvalidFormException
     */
    public function testPostShouldRaiseException()
    {
        $title = 'title1';

        $parameters = array('title' => $title);

        $form = $this->getMock('Symfony\Component\Form\FormInterface');
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));


        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $handler = $this->createHandler();
        $object = $handler->post($parameters);

    }

    protected function createHandler()
    {
        return new ObjectiveHandler($this->om, static::ENT_CLASS, $this->formFactory);
    }

    protected function getObjective()
    {
        $class = static::ENT_CLASS;

        return new $class();
    }
}
