<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\MeshSemanticTypeType;
use Ilios\CoreBundle\Entity\Manager\MeshSemanticTypeManager;
use Ilios\CoreBundle\Entity\MeshSemanticTypeInterface;

class MeshSemanticTypeHandler extends MeshSemanticTypeManager
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
     * @return MeshSemanticTypeInterface
     */
    public function post(array $parameters)
    {
        $meshSemanticType = $this->createMeshSemanticType();

        return $this->processForm($meshSemanticType, $parameters, 'POST');
    }

    /**
     * @param MeshSemanticTypeInterface $meshSemanticType
     * @param array $parameters
     *
     * @return MeshSemanticTypeInterface
     */
    public function put(
        MeshSemanticTypeInterface $meshSemanticType,
        array $parameters
    ) {
        return $this->processForm(
            $meshSemanticType,
            $parameters,
            'PUT'
        );
    }
    /**
     * @param MeshSemanticTypeInterface $meshSemanticType
     * @param array $parameters
     *
     * @return MeshSemanticTypeInterface
     */
    public function patch(
        MeshSemanticTypeInterface $meshSemanticType,
        array $parameters
    ) {
        return $this->processForm(
            $meshSemanticType,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param MeshSemanticTypeInterface $meshSemanticType
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return MeshSemanticTypeInterface
     */
    protected function processForm(
        MeshSemanticTypeInterface $meshSemanticType,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new MeshSemanticTypeType(),
            $meshSemanticType,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $meshSemanticType = $form->getData();
            $this->updateMeshSemanticType($meshSemanticType, true);

            return $meshSemanticType;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
