<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\AamcResourceTypeType;
use Ilios\CoreBundle\Entity\Manager\AamcResourceTypeManager;
use Ilios\CoreBundle\Entity\AamcResourceTypeInterface;

/**
 * Class AamcResourceTypeHandler
 * @package Ilios\CoreBundle\Handler
 */
class AamcResourceTypeHandler extends AamcResourceTypeManager
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
     * @return AamcResourceTypeInterface
     */
    public function post(array $parameters)
    {
        $aamcResourceType = $this->createAamcResourceType();

        return $this->processForm($aamcResourceType, $parameters, 'POST');
    }

    /**
     * @param AamcResourceTypeInterface $aamcResourceType
     * @param array $parameters
     *
     * @return AamcResourceTypeInterface
     */
    public function put(
        AamcResourceTypeInterface $aamcResourceType,
        array $parameters
    ) {
        return $this->processForm(
            $aamcResourceType,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param AamcResourceTypeInterface $aamcResourceType
     * @param array $parameters
     *
     * @return AamcResourceTypeInterface
     */
    public function patch(
        AamcResourceTypeInterface $aamcResourceType,
        array $parameters
    ) {
        return $this->processForm(
            $aamcResourceType,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param AamcResourceTypeInterface $aamcResourceType
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return AamcResourceTypeInterface
     */
    protected function processForm(
        AamcResourceTypeInterface $aamcResourceType,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            AamcResourceTypeType::class,
            $aamcResourceType,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
