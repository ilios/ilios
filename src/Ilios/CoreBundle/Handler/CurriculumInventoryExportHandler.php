<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\CurriculumInventoryExportType;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryExportManager;
use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;

/**
 * Class CurriculumInventoryExportHandler
 * @package Ilios\CoreBundle\Handler
 */
class CurriculumInventoryExportHandler extends CurriculumInventoryExportManager
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
     * @return CurriculumInventoryExportInterface
     */
    public function post(array $parameters)
    {
        $curriculumInventoryExport = $this->createCurriculumInventoryExport();

        return $this->processForm($curriculumInventoryExport, $parameters, 'POST');
    }

    /**
     * @param CurriculumInventoryExportInterface $curriculumInventoryExport
     * @param array $parameters
     *
     * @return CurriculumInventoryExportInterface
     */
    public function put(
        CurriculumInventoryExportInterface $curriculumInventoryExport,
        array $parameters
    ) {
        return $this->processForm(
            $curriculumInventoryExport,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param CurriculumInventoryExportInterface $curriculumInventoryExport
     * @param array $parameters
     *
     * @return CurriculumInventoryExportInterface
     */
    public function patch(
        CurriculumInventoryExportInterface $curriculumInventoryExport,
        array $parameters
    ) {
        return $this->processForm(
            $curriculumInventoryExport,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param CurriculumInventoryExportInterface $curriculumInventoryExport
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CurriculumInventoryExportInterface
     */
    protected function processForm(
        CurriculumInventoryExportInterface $curriculumInventoryExport,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            CurriculumInventoryExportType::class,
            $curriculumInventoryExport,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
