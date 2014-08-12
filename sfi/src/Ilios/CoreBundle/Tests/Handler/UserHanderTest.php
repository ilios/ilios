<?php

namespace Ilios\CoreBundle\Tests\Handler;

use Ilios\CoreBundle\Handler\UserHandler;
use Ilios\CoreBundle\Tests\TestCase;
use Mockery as m;

class UserHandlerTest extends TestCase
{
    const ENT_CLASS = 'Ilios\CoreBundle\Entity\User';

    /** @var UserHandler */
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
        $user = $this->getUser();
        $this->repository->shouldREceive('find')->once()
            ->with($id)->andReturn($user);

        $handler = $this->createHandler();

        $handler->get($id);
    }

    public function testGetAll()
    {
        $users = array(
            $this->getUser()->setFirstName('first'),
            $this->getUser()->setFirstName('second')
        );
        $this->repository->shouldREceive('findAll')->once()
            ->andReturn($users);

        $handler = $this->createHandler();

        $handler->getAll();
    }

    public function testPost()
    {
        $firstName = 'firstName';

        $parameters = array('firstName' => $firstName);

        $user = $this->getUser();
        $user->setFirstName($firstName);

        $form = m::mock('Symfony\Component\Form\Tests\FormInterface')
            ->shouldReceive('submit')->once()
            ->shouldReceive('isValid')->once()->andReturn(true)
            ->shouldReceive('getData')->once()->andReturn($user)
            ->getMock();
        
        $this->formFactory->shouldReceive('create')->once()
            ->andReturn($form)->getMock();
        
        $this->om->shouldReceive('persist')->once()->with($user);
        $this->om->shouldReceive('flush')->once();

        $handler = $this->createHandler();
        $object = $handler->post($parameters);

        $this->assertEquals($object, $user);
    }
    
    public function testPostShouldRaiseException()
    {
        $this->setExpectedException('Ilios\CoreBundle\Exception\InvalidFormException');
        
        $firstName = 'firstname';

        $parameters = array('firstName' => $firstName);

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
        return new UserHandler($this->om, self::ENT_CLASS, $this->formFactory);
    }

    protected function getUser()
    {
        $class = self::ENT_CLASS;

        return new $class();
    }
}
