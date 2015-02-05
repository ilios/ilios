<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\PublishEventType;
use Ilios\CoreBundle\Entity\Manager\PublishEventManager;
use Ilios\CoreBundle\Entity\PublishEventInterface;

class PublishEventHandler extends PublishEventManager
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param EntityManager $em
     * @param string $class
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManager $em, $class, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        parent::__construct($em, $class);
    }

    /**
     * @param array $parameters
     *
     * @return PublishEventInterface
     */
    public function post(array $parameters)
    {
        $publishEvent = $this->createPublishEvent();

        return $this->processForm($publishEvent, $parameters, 'POST');
    }

    /**
     * @param PublishEventInterface $publishEvent
     * @param array $parameters
     *
     * @return PublishEventInterface
     */
    public function put(PublishEventInterface $publishEvent, array $parameters)
    {
        return $this->processForm($publishEvent, $parameters, 'PUT');
    }

    /**
     * @param PublishEventInterface $publishEvent
     * @param array $parameters
     *
     * @return PublishEventInterface
     */
    public function patch(PublishEventInterface $publishEvent, array $parameters)
    {
        return $this->processForm($publishEvent, $parameters, 'PATCH');
    }

    /**
     * @param PublishEventInterface $publishEvent
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return PublishEventInterface
     */
    protected function processForm(PublishEventInterface $publishEvent, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new PublishEventType(), $publishEvent, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $publishEvent = $form->getData();
            $this->updatePublishEvent($publishEvent, true);

            return $publishEvent;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
