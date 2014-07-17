<?php
namespace Ilios\CoreBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

use Ilios\CoreBundle\Entity\Objective;
use Ilios\CoreBundle\Form\ObjectiveType;
use Ilios\CoreBundle\Exception\InvalidFormException;

class ObjectiveHandler
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;

    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
    }

    /**
     * Get an Objective.
     *
     * @param mixed $id
     *
     * @return Objective
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get all Objectives
     *
     * @return Objective[]
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Create a new Objective.
     *
     * @param Request $request
     *
     * @return Objective
     */
    public function post(array $parameters)
    {
        $objective = new $this->entityClass();
        return $this->processForm($objective, $parameters, 'POST');
    }

    /**
     * Edit an Obejctive, or create if it doesn't exist.
     *
     * @param Objective     $objective
     * @param array         $parameters
     *
     * @return Objective
     */
    public function put(Objective $objective, array $parameters)
    {
        return $this->processForm($objective, $parameters, 'PUT');
    }

    /**
     * Processes the form.
     *
     * @param Objective     $objective
     * @param array         $parameters
     * @param String        $method
     *
     * @return Objective
     *
     * @throws \Ilios\CoreBundle\Exception\InvalidFormException
     */
    private function processForm(Objective $objective, array $parameters, $method)
    {
        $form = $this->formFactory->create(
            new ObjectiveType(),
            $objective,
            array('method' => $method)
        );
        $form->submit($parameters);
        if ($form->isValid()) {
            //re-request the data for testability
            $objective = $form->getData();
            $this->om->persist($objective);
            $this->om->flush($objective);

            return $objective;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
