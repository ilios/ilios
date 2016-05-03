<?php

namespace Ilios\CoreBundle\Handler;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Form\FormFactoryInterface;

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
        $form = $this->formFactory->create(PermissionType::class, $permission, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
