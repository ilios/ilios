<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\CurriculumInventoryAcademicLevelType;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryAcademicLevelManager;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;

class CurriculumInventoryAcademicLevelHandler extends CurriculumInventoryAcademicLevelManager
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
            new CurriculumInventoryAcademicLevelType(),
            $curriculumInventoryAcademicLevel,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $curriculumInventoryAcademicLevel = $form->getData();
            $this->updateCurriculumInventoryAcademicLevel($curriculumInventoryAcademicLevel, true);

            return $curriculumInventoryAcademicLevel;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
