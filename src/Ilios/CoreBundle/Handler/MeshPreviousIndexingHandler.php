<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\MeshPreviousIndexingType;
use Ilios\CoreBundle\Entity\Manager\MeshPreviousIndexingManager;
use Ilios\CoreBundle\Entity\MeshPreviousIndexingInterface;

/**
 * Class MeshPreviousIndexingHandler
 * @package Ilios\CoreBundle\Handler
 */
class MeshPreviousIndexingHandler extends MeshPreviousIndexingManager
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
     * @return MeshPreviousIndexingInterface
     */
    public function post(array $parameters)
    {
        $meshPreviousIndexing = $this->createMeshPreviousIndexing();

        return $this->processForm($meshPreviousIndexing, $parameters, 'POST');
    }

    /**
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     * @param array $parameters
     *
     * @return MeshPreviousIndexingInterface
     */
    public function put(
        MeshPreviousIndexingInterface $meshPreviousIndexing,
        array $parameters
    ) {
        return $this->processForm(
            $meshPreviousIndexing,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     * @param array $parameters
     *
     * @return MeshPreviousIndexingInterface
     */
    public function patch(
        MeshPreviousIndexingInterface $meshPreviousIndexing,
        array $parameters
    ) {
        return $this->processForm(
            $meshPreviousIndexing,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return MeshPreviousIndexingInterface
     */
    protected function processForm(
        MeshPreviousIndexingInterface $meshPreviousIndexing,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            MeshPreviousIndexingType::class,
            $meshPreviousIndexing,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
