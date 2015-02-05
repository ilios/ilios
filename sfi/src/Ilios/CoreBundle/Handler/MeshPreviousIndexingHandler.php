<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\MeshPreviousIndexingType;
use Ilios\CoreBundle\Entity\Manager\MeshPreviousIndexingManager;
use Ilios\CoreBundle\Entity\MeshPreviousIndexingInterface;

class MeshPreviousIndexingHandler extends MeshPreviousIndexingManager
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
    public function put(MeshPreviousIndexingInterface $meshPreviousIndexing, array $parameters)
    {
        return $this->processForm($meshPreviousIndexing, $parameters, 'PUT');
    }

    /**
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     * @param array $parameters
     *
     * @return MeshPreviousIndexingInterface
     */
    public function patch(MeshPreviousIndexingInterface $meshPreviousIndexing, array $parameters)
    {
        return $this->processForm($meshPreviousIndexing, $parameters, 'PATCH');
    }

    /**
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return MeshPreviousIndexingInterface
     */
    protected function processForm(MeshPreviousIndexingInterface $meshPreviousIndexing, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new MeshPreviousIndexingType(), $meshPreviousIndexing, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $meshPreviousIndexing = $form->getData();
            $this->updateMeshPreviousIndexing($meshPreviousIndexing, true);

            return $meshPreviousIndexing;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
