<?php
namespace Ilios\CoreBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

use Ilios\CoreBundle\Entity\User;
use Ilios\CoreBundle\Form\UserType;
use Ilios\CoreBundle\Exception\InvalidFormException;

class UserHandler
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
     * Get an User.
     *
     * @param mixed $id
     *
     * @return User
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get all Users
     *
     * @return User[]
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Create a new User.
     *
     * @param Request $request
     *
     * @return User
     */
    public function post(array $parameters)
    {
        $user = new $this->entityClass();
        return $this->processForm($user, $parameters, 'POST');
    }

    /**
     * Edit an Obejctive, or create if it doesn't exist.
     *
     * @param User     $user
     * @param array         $parameters
     *
     * @return User
     */
    public function put(User $user, array $parameters)
    {
        return $this->processForm($user, $parameters, 'PUT');
    }

    /**
     * Processes the form.
     *
     * @param User     $user
     * @param array         $parameters
     * @param String        $method
     *
     * @return User
     *
     * @throws \Ilios\CoreBundle\Exception\InvalidFormException
     */
    private function processForm(User $user, array $parameters, $method)
    {
        $form = $this->formFactory->create(
            new UserType(),
            $user,
            array('method' => $method)
        );
        $form->submit($parameters);
        if ($form->isValid()) {
            //re-request the data for testability
            $user = $form->getData();
            $this->om->persist($user);
            $this->om->flush($user);

            return $user;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
