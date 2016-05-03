<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\CurriculumInventoryAcademicLevelType;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryAcademicLevelManager;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;

/**
 * Class CurriculumInventoryAcademicLevelHandler
 * @package Ilios\CoreBundle\Handler
 */
class CurriculumInventoryAcademicLevelHandler extends CurriculumInventoryAcademicLevelManager
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
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function post(array $parameters)
    {
        $curriculumInventoryAcademicLevel = $this->createCurriculumInventoryAcademicLevel();

        return $this->processForm($curriculumInventoryAcademicLevel, $parameters, 'POST');
    }

    /**
     * @param CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     * @param array $parameters
     *
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function put(
        CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel,
        array $parameters
    ) {
        return $this->processForm(
            $curriculumInventoryAcademicLevel,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     * @param array $parameters
     *
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function patch(
        CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel,
        array $parameters
    ) {
        return $this->processForm(
            $curriculumInventoryAcademicLevel,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CurriculumInventoryAcademicLevelInterface
     */
    protected function processForm(
        CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            CurriculumInventoryAcademicLevelType::class,
            $curriculumInventoryAcademicLevel,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
