<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\AlertType;
use Ilios\CoreBundle\Entity\Manager\AlertManager;
use Ilios\CoreBundle\Entity\AlertInterface;

class AlertHandler extends AlertManager
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
     * @return AlertInterface
     */
    public function post(array $parameters)
    {
        $alert = $this->createAlert();

        return $this->processForm($alert, $parameters, 'POST');
    }

    /**
     * @param AlertInterface $alert
     * @param array $parameters
     *
     * @return AlertInterface
     */
    public function put(AlertInterface $alert, array $parameters)
    {
        return $this->processForm($alert, $parameters, 'PUT');
    }

    /**
     * @param AlertInterface $alert
     * @param array $parameters
     *
     * @return AlertInterface
     */
    public function patch(AlertInterface $alert, array $parameters)
    {
        return $this->processForm($alert, $parameters, 'PATCH');
    }

    /**
     * @param AlertInterface $alert
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return AlertInterface
     */
    protected function processForm(AlertInterface $alert, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new AlertType(), $alert, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $alert = $form->getData();
            $this->updateAlert($alert, true);

            return $alert;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
