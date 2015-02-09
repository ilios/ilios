<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\UserRoleType;
use Ilios\CoreBundle\Entity\Manager\UserRoleManager;
use Ilios\CoreBundle\Entity\UserRoleInterface;

class UserRoleHandler extends UserRoleManager
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
     * @return UserRoleInterface
     */
    public function post(array $parameters)
    {
        $userRole = $this->createUserRole();

        return $this->processForm($userRole, $parameters, 'POST');
    }

    /**
     * @param UserRoleInterface $userRole
     * @param array $parameters
     *
     * @return UserRoleInterface
     */
    public function put(
        UserRoleInterface $userRole,
        array $parameters
    ) {
        return $this->processForm(
            $userRole,
            $parameters,
            'PUT'
        );
    }
    /**
     * @param UserRoleInterface $userRole
     * @param array $parameters
     *
     * @return UserRoleInterface
     */
    public function patch(
        UserRoleInterface $userRole,
        array $parameters
    ) {
        return $this->processForm(
            $userRole,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param UserRoleInterface $userRole
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return UserRoleInterface
     */
    protected function processForm(
        UserRoleInterface $userRole,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new UserRoleType(),
            $userRole,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $userRole = $form->getData();
            $this->updateUserRole($userRole, true);

            return $userRole;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
