<?php

namespace Ilios\CoreBundle\Tests\Handler;

use Ilios\CoreBundle\Handler\ObjectiveHandler;
use Ilios\CoreBundle\Tests\TestCase;
use Mockery as m;

class ObjectiveHandlerTest extends TestCase
{
    const ENT_CLASS = 'Ilios\CoreBundle\Entity\Objective';

    /** @var ObjectiveHandler */
    protected $handler;
    
    protected $om;
    protected $repository;

    public function setUp()
    {
        if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }

        $class = m::mock('Doctrine\Common\Persistence\Mapping\ClassMetadata')
            ->shouldReceive('getName')->andReturn(self::ENT_CLASS);
        $this->repository = m::mock('Doctrine\Common\Persistence\ObjectRepository');
        $this->om = m::mock('Doctrine\Common\Persistence\ObjectManager')
            ->shouldReceive('getRepository')->once()->with(self::ENT_CLASS)
            ->andReturn($this->repository)
            ->shouldReceive('getClassMetadata')->with(self::ENT_CLASS)
            ->andReturn($class)
            ->getMock();
        $this->formFactory = m::mock('Symfony\Component\Form\FormFactoryInterface');
    }

    public function testGet()
    {
        $id = 1;
        $objective = $this->getObjective();
        $this->repository->shouldREceive('find')->once()
            ->with($id)->andReturn($objective);

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
        $this->repository->shouldREceive('findAll')->once()
            ->andReturn($objectives);

        $handler = $this->createHandler();

        $handler->getAll();
    }

    public function testPost()
    {
        $title = 'title1';

        $parameters = array('title' => $title);

        $objective = $this->getObjective();
        $objective->setTitle($title);

        $form = m::mock('Symfony\Component\Form\Tests\FormInterface')
            ->shouldReceive('submit')->once()
            ->shouldReceive('isValid')->once()->andReturn(true)
            ->shouldReceive('getData')->once()->andReturn($objective)
            ->getMock();
        
        $this->formFactory->shouldReceive('create')->once()
            ->andReturn($form)->getMock();
        
        $this->om->shouldReceive('persist')->once()->with($objective);
        $this->om->shouldReceive('flush')->once();

        $handler = $this->createHandler();
        $object = $handler->post($parameters);

        $this->assertEquals($object, $objective);
    }
    
    public function testPostShouldRaiseException()
    {
        $this->setExpectedException('Ilios\CoreBundle\Exception\InvalidFormException');
        
        $title = 'title1';

        $parameters = array('title' => $title);

        $form = m::mock('Symfony\Component\Form\FormInterface')
            ->shouldReceive('submit')->once()
            ->shouldReceive('isValid')->once()->andReturn(false)
            ->getMock();

        $this->formFactory->shouldReceive('create')->once()->andReturn($form);

        $handler = $this->createHandler();
        $handler->post($parameters);
    }

    protected function createHandler()
    {
        return new ObjectiveHandler($this->om, self::ENT_CLASS, $this->formFactory);
    }

    protected function getObjective()
    {
        $class = self::ENT_CLASS;

        return new $class();
    }
}
