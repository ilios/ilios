<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\OfferingType;
use Ilios\CoreBundle\Entity\Manager\OfferingManager;
use Ilios\CoreBundle\Entity\OfferingInterface;

/**
 * Class OfferingHandler
 * @package Ilios\CoreBundle\Handler
 */
class OfferingHandler extends OfferingManager
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
     * @return OfferingInterface
     */
    public function post(array $parameters)
    {
        $offering = $this->createOffering();

        return $this->processForm($offering, $parameters, 'POST');
    }

    /**
     * @param OfferingInterface $offering
     * @param array $parameters
     *
     * @return OfferingInterface
     */
    public function put(
        OfferingInterface $offering,
        array $parameters
    ) {
        return $this->processForm(
            $offering,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param OfferingInterface $offering
     * @param array $parameters
     *
     * @return OfferingInterface
     */
    public function patch(
        OfferingInterface $offering,
        array $parameters
    ) {
        return $this->processForm(
            $offering,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param OfferingInterface $offering
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return OfferingInterface
     */
    protected function processForm(
        OfferingInterface $offering,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            OfferingType::class,
            $offering,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
