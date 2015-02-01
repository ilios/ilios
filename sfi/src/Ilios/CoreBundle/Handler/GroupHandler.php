<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\GroupType;
use Ilios\CoreBundle\Entity\Manager\GroupManager;
use Ilios\CoreBundle\Entity\GroupInterface;

class GroupHandler extends GroupManager
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
     * @return GroupInterface
     */
    public function post(array $parameters)
    {
        $group = $this->createGroup();

        return $this->processForm($group, $parameters, 'POST');
    }

    /**
     * @param GroupInterface $group
     * @param array $parameters
     *
     * @return GroupInterface
     */
    public function put(GroupInterface $group, array $parameters)
    {
        return $this->processForm($group, $parameters, 'PUT');
    }

    /**
     * @param GroupInterface $group
     * @param array $parameters
     *
     * @return GroupInterface
     */
    public function patch(GroupInterface $group, array $parameters)
    {
        return $this->processForm($group, $parameters, 'PATCH');
    }

    /**
     * @param GroupInterface $group
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return GroupInterface
     */
    protected function processForm(GroupInterface $group, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new GroupType(), $group, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $group = $form->getData();
            $this->updateGroup($group, true);

            return $group;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
