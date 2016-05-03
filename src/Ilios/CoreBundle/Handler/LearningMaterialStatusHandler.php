<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\LearningMaterialStatusType;
use Ilios\CoreBundle\Entity\Manager\LearningMaterialStatusManager;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

/**
 * Class LearningMaterialStatusHandler
 * @package Ilios\CoreBundle\Handler
 */
class LearningMaterialStatusHandler extends LearningMaterialStatusManager
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
     * @return LearningMaterialStatusInterface
     */
    public function post(array $parameters)
    {
        $learningMaterialStatus = $this->createLearningMaterialStatus();

        return $this->processForm($learningMaterialStatus, $parameters, 'POST');
    }

    /**
     * @param LearningMaterialStatusInterface $learningMaterialStatus
     * @param array $parameters
     *
     * @return LearningMaterialStatusInterface
     */
    public function put(
        LearningMaterialStatusInterface $learningMaterialStatus,
        array $parameters
    ) {
        return $this->processForm(
            $learningMaterialStatus,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param LearningMaterialStatusInterface $learningMaterialStatus
     * @param array $parameters
     *
     * @return LearningMaterialStatusInterface
     */
    public function patch(
        LearningMaterialStatusInterface $learningMaterialStatus,
        array $parameters
    ) {
        return $this->processForm(
            $learningMaterialStatus,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param LearningMaterialStatusInterface $learningMaterialStatus
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return LearningMaterialStatusInterface
     */
    protected function processForm(
        LearningMaterialStatusInterface $learningMaterialStatus,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            LearningMaterialStatusType::class,
            $learningMaterialStatus,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
