<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\LearningMaterialType;
use Ilios\CoreBundle\Entity\Manager\LearningMaterialManager;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;

/**
 * Class LearningMaterialHandler
 * @package Ilios\CoreBundle\Handler
 */
class LearningMaterialHandler extends LearningMaterialManager
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
     * @return LearningMaterialInterface
     */
    public function post(array $parameters)
    {
        $learningMaterial = $this->createLearningMaterial();

        return $this->processForm($learningMaterial, $parameters, 'POST');
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     * @param array $parameters
     *
     * @return LearningMaterialInterface
     */
    public function put(
        LearningMaterialInterface $learningMaterial,
        array $parameters
    ) {
        return $this->processForm(
            $learningMaterial,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     * @param array $parameters
     *
     * @return LearningMaterialInterface
     */
    public function patch(
        LearningMaterialInterface $learningMaterial,
        array $parameters
    ) {
        return $this->processForm(
            $learningMaterial,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return LearningMaterialInterface
     */
    protected function processForm(
        LearningMaterialInterface $learningMaterial,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            LearningMaterialType::class,
            $learningMaterial,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
