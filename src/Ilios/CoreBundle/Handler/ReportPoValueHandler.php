<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\ReportPoValueType;
use Ilios\CoreBundle\Entity\Manager\ReportPoValueManager;
use Ilios\CoreBundle\Entity\ReportPoValueInterface;

/**
 * Class ReportPoValueHandler
 * @package Ilios\CoreBundle\Handler
 */
class ReportPoValueHandler extends ReportPoValueManager
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
            $this->updateReportPoValue($reportPoValue, true, ('PUT' === $method || 'PATCH' === $method));

            return $reportPoValue;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
