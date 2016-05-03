<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\CourseLearningMaterialType;
use Ilios\CoreBundle\Entity\Manager\CourseLearningMaterialManager;
use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;

/**
 * Class CourseLearningMaterialHandler
 * @package Ilios\CoreBundle\Handler
 */
class CourseLearningMaterialHandler extends CourseLearningMaterialManager
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
     * @return CourseLearningMaterialInterface
     */
    public function post(array $parameters)
    {
        $courseLearningMaterial = $this->createCourseLearningMaterial();

        return $this->processForm($courseLearningMaterial, $parameters, 'POST');
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     * @param array $parameters
     *
     * @return CourseLearningMaterialInterface
     */
    public function put(
        CourseLearningMaterialInterface $courseLearningMaterial,
        array $parameters
    ) {
        return $this->processForm(
            $courseLearningMaterial,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     * @param array $parameters
     *
     * @return CourseLearningMaterialInterface
     */
    public function patch(
        CourseLearningMaterialInterface $courseLearningMaterial,
        array $parameters
    ) {
        return $this->processForm(
            $courseLearningMaterial,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CourseLearningMaterialInterface
     */
    protected function processForm(
        CourseLearningMaterialInterface $courseLearningMaterial,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            CourseLearningMaterialType::class,
            $courseLearningMaterial,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
