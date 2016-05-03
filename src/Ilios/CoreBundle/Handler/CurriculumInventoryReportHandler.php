<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\CurriculumInventoryReportType;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryReportHandler
 * @package Ilios\CoreBundle\Handler
 */
class CurriculumInventoryReportHandler extends CurriculumInventoryReportManager
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
     * @return CurriculumInventoryReportInterface
     */
    public function post(array $parameters)
    {
        $curriculumInventoryReport = $this->createCurriculumInventoryReport();

        return $this->processForm($curriculumInventoryReport, $parameters, 'POST');
    }

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     * @param array $parameters
     *
     * @return CurriculumInventoryReportInterface
     */
    public function put(
        CurriculumInventoryReportInterface $curriculumInventoryReport,
        array $parameters
    ) {
        return $this->processForm(
            $curriculumInventoryReport,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     * @param array $parameters
     *
     * @return CurriculumInventoryReportInterface
     */
    public function patch(
        CurriculumInventoryReportInterface $curriculumInventoryReport,
        array $parameters
    ) {
        return $this->processForm(
            $curriculumInventoryReport,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CurriculumInventoryReportInterface
     */
    protected function processForm(
        CurriculumInventoryReportInterface $curriculumInventoryReport,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            CurriculumInventoryReportType::class,
            $curriculumInventoryReport,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
