<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\PermissionType;
use Ilios\CoreBundle\Entity\Manager\PermissionManager;
use Ilios\CoreBundle\Entity\PermissionInterface;

class PermissionHandler extends PermissionManager
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
     * @return PermissionInterface
     */
    public function post(array $parameters)
    {
        $permission = $this->createPermission();

        return $this->processForm($permission, $parameters, 'POST');
    }

    /**
     * @param PermissionInterface $permission
     * @param array $parameters
     *
     * @return PermissionInterface
     */
    public function put(PermissionInterface $permission, array $parameters)
    {
        return $this->processForm($permission, $parameters, 'PUT');
    }

    /**
     * @param PermissionInterface $permission
     * @param array $parameters
     *
     * @return PermissionInterface
     */
    public function patch(PermissionInterface $permission, array $parameters)
    {
        return $this->processForm($permission, $parameters, 'PATCH');
    }

    /**
     * @param PermissionInterface $permission
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return PermissionInterface
     */
    protected function processForm(PermissionInterface $permission, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new PermissionType(), $permission, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $permission = $form->getData();
            $this->updatePermission($permission, true);

            return $permission;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
