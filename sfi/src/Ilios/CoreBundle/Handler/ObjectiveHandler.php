<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\ObjectiveType;
use Ilios\CoreBundle\Entity\Manager\ObjectiveManager;
use Ilios\CoreBundle\Entity\ObjectiveInterface;

class ObjectiveHandler extends ObjectiveManager
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
     * @return ObjectiveInterface
     */
    public function post(array $parameters)
    {
        $objective = $this->createObjective();

        return $this->processForm($objective, $parameters, 'POST');
    }

    /**
     * @param ObjectiveInterface $objective
     * @param array $parameters
     *
     * @return ObjectiveInterface
     */
    public function put(
        ObjectiveInterface $objective,
        array $parameters
    ) {
        return $this->processForm(
            $objective,
            $parameters,
            'PUT'
        );
    }
    /**
     * @param ObjectiveInterface $objective
     * @param array $parameters
     *
     * @return ObjectiveInterface
     */
    public function patch(
        ObjectiveInterface $objective,
        array $parameters
    ) {
        return $this->processForm(
            $objective,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param ObjectiveInterface $objective
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return ObjectiveInterface
     */
    protected function processForm(
        ObjectiveInterface $objective,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new ObjectiveType(),
            $objective,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $objective = $form->getData();
            $this->updateObjective($objective, true);

            return $objective;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
