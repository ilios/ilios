<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\MeshUserSelectionType;
use Ilios\CoreBundle\Entity\Manager\MeshUserSelectionManager;
use Ilios\CoreBundle\Entity\MeshUserSelectionInterface;

/**
 * Class MeshUserSelectionHandler
 * @package Ilios\CoreBundle\Handler
 */
class MeshUserSelectionHandler extends MeshUserSelectionManager
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param Registry $em
     * @param string $class
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(Registry $em, $class, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        parent::__construct($em, $class);
    }

    /**
     * @param array $parameters
     *
     * @return MeshUserSelectionInterface
     */
    public function post(array $parameters)
    {
        $meshUserSelection = $this->createMeshUserSelection();

        return $this->processForm($meshUserSelection, $parameters, 'POST');
    }

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     * @param array $parameters
     *
     * @return MeshUserSelectionInterface
     */
    public function put(
        MeshUserSelectionInterface $meshUserSelection,
        array $parameters
    ) {
        return $this->processForm(
            $meshUserSelection,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     * @param array $parameters
     *
     * @return MeshUserSelectionInterface
     */
    public function patch(
        MeshUserSelectionInterface $meshUserSelection,
        array $parameters
    ) {
        return $this->processForm(
            $meshUserSelection,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return MeshUserSelectionInterface
     */
    protected function processForm(
        MeshUserSelectionInterface $meshUserSelection,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new MeshUserSelectionType(),
            $meshUserSelection,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
