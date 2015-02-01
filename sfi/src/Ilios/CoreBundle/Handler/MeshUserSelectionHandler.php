<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\MeshUserSelectionType;
use Ilios\CoreBundle\Entity\Manager\MeshUserSelectionManager;
use Ilios\CoreBundle\Entity\MeshUserSelectionInterface;

class MeshUserSelectionHandler extends MeshUserSelectionManager
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
    public function put(MeshUserSelectionInterface $meshUserSelection, array $parameters)
    {
        return $this->processForm($meshUserSelection, $parameters, 'PUT');
    }

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     * @param array $parameters
     *
     * @return MeshUserSelectionInterface
     */
    public function patch(MeshUserSelectionInterface $meshUserSelection, array $parameters)
    {
        return $this->processForm($meshUserSelection, $parameters, 'PATCH');
    }

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return MeshUserSelectionInterface
     */
    protected function processForm(MeshUserSelectionInterface $meshUserSelection, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new MeshUserSelectionType(), $meshUserSelection, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $meshUserSelection = $form->getData();
            $this->updateMeshUserSelection($meshUserSelection, true);

            return $meshUserSelection;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
