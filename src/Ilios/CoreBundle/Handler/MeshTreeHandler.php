<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\MeshTreeType;
use Ilios\CoreBundle\Entity\Manager\MeshTreeManager;
use Ilios\CoreBundle\Entity\MeshTreeInterface;

/**
 * Class MeshTreeHandler
 * @package Ilios\CoreBundle\Handler
 */
class MeshTreeHandler extends MeshTreeManager
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
     * @return MeshTreeInterface
     */
    public function post(array $parameters)
    {
        $meshTree = $this->createMeshTree();

        return $this->processForm($meshTree, $parameters, 'POST');
    }

    /**
     * @param MeshTreeInterface $meshTree
     * @param array $parameters
     *
     * @return MeshTreeInterface
     */
    public function put(
        MeshTreeInterface $meshTree,
        array $parameters
    ) {
        return $this->processForm(
            $meshTree,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param MeshTreeInterface $meshTree
     * @param array $parameters
     *
     * @return MeshTreeInterface
     */
    public function patch(
        MeshTreeInterface $meshTree,
        array $parameters
    ) {
        return $this->processForm(
            $meshTree,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param MeshTreeInterface $meshTree
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return MeshTreeInterface
     */
    protected function processForm(
        MeshTreeInterface $meshTree,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            MeshTreeType::class,
            $meshTree,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
