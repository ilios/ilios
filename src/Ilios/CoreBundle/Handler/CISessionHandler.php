<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\CISessionType;
use Ilios\CoreBundle\Entity\Manager\CISessionManager;
use Ilios\CoreBundle\Entity\CISessionInterface;

class CISessionHandler extends CISessionManager
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
     * @return CISessionInterface
     */
    public function post(array $parameters)
    {
        $cISession = $this->createCISession();

        return $this->processForm($cISession, $parameters, 'POST');
    }

    /**
     * @param CISessionInterface $cISession
     * @param array $parameters
     *
     * @return CISessionInterface
     */
    public function put(CISessionInterface $cISession, array $parameters)
    {
        return $this->processForm($cISession, $parameters, 'PUT');
    }

    /**
     * @param CISessionInterface $cISession
     * @param array $parameters
     *
     * @return CISessionInterface
     */
    public function patch(CISessionInterface $cISession, array $parameters)
    {
        return $this->processForm($cISession, $parameters, 'PATCH');
    }

    /**
     * @param CISessionInterface $cISession
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CISessionInterface
     */
    protected function processForm(CISessionInterface $cISession, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new CISessionType(), $cISession, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }
        return $form->getData();
    }
}
