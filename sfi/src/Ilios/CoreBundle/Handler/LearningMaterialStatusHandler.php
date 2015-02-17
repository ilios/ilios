<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\LearningMaterialStatusType;
use Ilios\CoreBundle\Entity\Manager\LearningMaterialStatusManager;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

class LearningMaterialStatusHandler extends LearningMaterialStatusManager
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
            new LearningMaterialStatusType(),
            $learningMaterialStatus,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $learningMaterialStatus = $form->getData();
            $this->updateLearningMaterialStatus($learningMaterialStatus, true);

            return $learningMaterialStatus;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
