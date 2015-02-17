<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\ReportPoValueType;
use Ilios\CoreBundle\Entity\Manager\ReportPoValueManager;
use Ilios\CoreBundle\Entity\ReportPoValueInterface;

class ReportPoValueHandler extends ReportPoValueManager
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
     * @return ReportPoValueInterface
     */
    public function post(array $parameters)
    {
        $reportPoValue = $this->createReportPoValue();

        return $this->processForm($reportPoValue, $parameters, 'POST');
    }

    /**
     * @param ReportPoValueInterface $reportPoValue
     * @param array $parameters
     *
     * @return ReportPoValueInterface
     */
    public function put(
        ReportPoValueInterface $reportPoValue,
        array $parameters
    ) {
        return $this->processForm(
            $reportPoValue,
            $parameters,
            'PUT'
        );
    }
    /**
     * @param ReportPoValueInterface $reportPoValue
     * @param array $parameters
     *
     * @return ReportPoValueInterface
     */
    public function patch(
        ReportPoValueInterface $reportPoValue,
        array $parameters
    ) {
        return $this->processForm(
            $reportPoValue,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param ReportPoValueInterface $reportPoValue
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return ReportPoValueInterface
     */
    protected function processForm(
        ReportPoValueInterface $reportPoValue,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new ReportPoValueType(),
            $reportPoValue,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $reportPoValue = $form->getData();
            $this->updateReportPoValue($reportPoValue, true);

            return $reportPoValue;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
