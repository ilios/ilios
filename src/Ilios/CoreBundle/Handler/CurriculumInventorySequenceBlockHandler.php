<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\CurriculumInventorySequenceBlockType;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventorySequenceBlockManager;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;

/**
 * Class CurriculumInventorySequenceBlockHandler
 * @package Ilios\CoreBundle\Handler
 */
class CurriculumInventorySequenceBlockHandler extends CurriculumInventorySequenceBlockManager
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
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function post(array $parameters)
    {
        $curriculumInventorySequenceBlock = $this->createCurriculumInventorySequenceBlock();

        return $this->processForm($curriculumInventorySequenceBlock, $parameters, 'POST');
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     * @param array $parameters
     *
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function put(
        CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock,
        array $parameters
    ) {
        return $this->processForm(
            $curriculumInventorySequenceBlock,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     * @param array $parameters
     *
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function patch(
        CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock,
        array $parameters
    ) {
        return $this->processForm(
            $curriculumInventorySequenceBlock,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CurriculumInventorySequenceBlockInterface
     */
    protected function processForm(
        CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            CurriculumInventorySequenceBlockType::class,
            $curriculumInventorySequenceBlock,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
