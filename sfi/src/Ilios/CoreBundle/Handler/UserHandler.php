<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\UserType;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\UserInterface;

class UserHandler extends UserManager
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
     * @return UserInterface
     */
    public function post(array $parameters)
    {
        $user = $this->createUser();

        return $this->processForm($user, $parameters, 'POST');
    }

    /**
     * @param UserInterface $user
     * @param array $parameters
     *
     * @return UserInterface
     */
    public function put(UserInterface $user, array $parameters)
    {
        return $this->processForm($user, $parameters, 'PUT');
    }

    /**
     * @param UserInterface $user
     * @param array $parameters
     *
     * @return UserInterface
     */
    public function patch(UserInterface $user, array $parameters)
    {
        return $this->processForm($user, $parameters, 'PATCH');
    }

    /**
     * @param UserInterface $user
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return UserInterface
     */
    protected function processForm(UserInterface $user, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new UserType(), $user, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $user = $form->getData();
            $this->updateUser($user, true);

            return $user;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
