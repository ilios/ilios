<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\LearningMaterialUserRoleType;
use Ilios\CoreBundle\Entity\Manager\LearningMaterialUserRoleManager;
use Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface;

/**
 * Class LearningMaterialUserRoleHandler
 * @package Ilios\CoreBundle\Handler
 */
class LearningMaterialUserRoleHandler extends LearningMaterialUserRoleManager
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
     * @return LearningMaterialUserRoleInterface
     */
    public function post(array $parameters)
    {
        $learningMaterialUserRole = $this->createLearningMaterialUserRole();

        return $this->processForm($learningMaterialUserRole, $parameters, 'POST');
    }

    /**
     * @param LearningMaterialUserRoleInterface $learningMaterialUserRole
     * @param array $parameters
     *
     * @return LearningMaterialUserRoleInterface
     */
    public function put(
        LearningMaterialUserRoleInterface $learningMaterialUserRole,
        array $parameters
    ) {
        return $this->processForm(
            $learningMaterialUserRole,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param LearningMaterialUserRoleInterface $learningMaterialUserRole
     * @param array $parameters
     *
     * @return LearningMaterialUserRoleInterface
     */
    public function patch(
        LearningMaterialUserRoleInterface $learningMaterialUserRole,
        array $parameters
    ) {
        return $this->processForm(
            $learningMaterialUserRole,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param LearningMaterialUserRoleInterface $learningMaterialUserRole
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return LearningMaterialUserRoleInterface
     */
    protected function processForm(
        LearningMaterialUserRoleInterface $learningMaterialUserRole,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            LearningMaterialUserRoleType::class,
            $learningMaterialUserRole,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
