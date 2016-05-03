<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\MeshConceptType;
use Ilios\CoreBundle\Entity\Manager\MeshConceptManager;
use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * Class MeshConceptHandler
 * @package Ilios\CoreBundle\Handler
 */
class MeshConceptHandler extends MeshConceptManager
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
     * @return MeshConceptInterface
     */
    public function post(array $parameters)
    {
        $meshConcept = $this->createMeshConcept();

        return $this->processForm($meshConcept, $parameters, 'POST');
    }

    /**
     * @param MeshConceptInterface $meshConcept
     * @param array $parameters
     *
     * @return MeshConceptInterface
     */
    public function put(
        MeshConceptInterface $meshConcept,
        array $parameters
    ) {
        return $this->processForm(
            $meshConcept,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param MeshConceptInterface $meshConcept
     * @param array $parameters
     *
     * @return MeshConceptInterface
     */
    public function patch(
        MeshConceptInterface $meshConcept,
        array $parameters
    ) {
        return $this->processForm(
            $meshConcept,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param MeshConceptInterface $meshConcept
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return MeshConceptInterface
     */
    protected function processForm(
        MeshConceptInterface $meshConcept,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            MeshConceptType::class,
            $meshConcept,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
