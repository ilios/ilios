<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\ReportType;
use Ilios\CoreBundle\Entity\Manager\ReportManager;
use Ilios\CoreBundle\Entity\ReportInterface;

class ReportHandler extends ReportManager
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
     * @return ReportInterface
     */
    public function post(array $parameters)
    {
        $report = $this->createReport();

        return $this->processForm($report, $parameters, 'POST');
    }

    /**
     * @param ReportInterface $report
     * @param array $parameters
     *
     * @return ReportInterface
     */
    public function put(ReportInterface $report, array $parameters)
    {
        return $this->processForm($report, $parameters, 'PUT');
    }

    /**
     * @param ReportInterface $report
     * @param array $parameters
     *
     * @return ReportInterface
     */
    public function patch(ReportInterface $report, array $parameters)
    {
        return $this->processForm($report, $parameters, 'PATCH');
    }

    /**
     * @param ReportInterface $report
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return ReportInterface
     */
    protected function processForm(ReportInterface $report, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new ReportType(), $report, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $report = $form->getData();
            $this->updateReport($report, true);

            return $report;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
