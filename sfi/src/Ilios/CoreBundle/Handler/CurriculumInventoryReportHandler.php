<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\CurriculumInventoryReportType;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

class CurriculumInventoryReportHandler extends CurriculumInventoryReportManager
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
    public function put(CurriculumInventoryReportInterface $curriculumInventoryReport, array $parameters)
    {
        return $this->processForm($curriculumInventoryReport, $parameters, 'PUT');
    }

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     * @param array $parameters
     *
     * @return CurriculumInventoryReportInterface
     */
    public function patch(CurriculumInventoryReportInterface $curriculumInventoryReport, array $parameters)
    {
        return $this->processForm($curriculumInventoryReport, $parameters, 'PATCH');
    }

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CurriculumInventoryReportInterface
     */
    protected function processForm(CurriculumInventoryReportInterface $curriculumInventoryReport, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new CurriculumInventoryReportType(), $curriculumInventoryReport, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $curriculumInventoryReport = $form->getData();
            $this->updateCurriculumInventoryReport($curriculumInventoryReport, true);

            return $curriculumInventoryReport;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
