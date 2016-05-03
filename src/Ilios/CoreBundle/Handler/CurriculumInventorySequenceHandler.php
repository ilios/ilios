<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\CurriculumInventorySequenceType;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventorySequenceManager;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;

/**
 * Class CurriculumInventorySequenceHandler
 * @package Ilios\CoreBundle\Handler
 */
class CurriculumInventorySequenceHandler extends CurriculumInventorySequenceManager
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
     * @return CurriculumInventorySequenceInterface
     */
    public function post(array $parameters)
    {
        $curriculumInventorySequence = $this->createCurriculumInventorySequence();

        return $this->processForm($curriculumInventorySequence, $parameters, 'POST');
    }

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     * @param array $parameters
     *
     * @return CurriculumInventorySequenceInterface
     */
    public function put(
        CurriculumInventorySequenceInterface $curriculumInventorySequence,
        array $parameters
    ) {
        return $this->processForm(
            $curriculumInventorySequence,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     * @param array $parameters
     *
     * @return CurriculumInventorySequenceInterface
     */
    public function patch(
        CurriculumInventorySequenceInterface $curriculumInventorySequence,
        array $parameters
    ) {
        return $this->processForm(
            $curriculumInventorySequence,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CurriculumInventorySequenceInterface
     */
    protected function processForm(
        CurriculumInventorySequenceInterface $curriculumInventorySequence,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            CurriculumInventorySequenceType::class,
            $curriculumInventorySequence,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
