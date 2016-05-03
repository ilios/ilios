<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\AamcPcrsType;
use Ilios\CoreBundle\Entity\Manager\AamcPcrsManager;
use Ilios\CoreBundle\Entity\AamcPcrsInterface;

/**
 * Class AamcPcrsHandler
 * @package Ilios\CoreBundle\Handler
 */
class AamcPcrsHandler extends AamcPcrsManager
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
     * @return AamcPcrsInterface
     */
    public function post(array $parameters)
    {
        $aamcPcrs = $this->createAamcPcrs();

        return $this->processForm($aamcPcrs, $parameters, 'POST');
    }

    /**
     * @param AamcPcrsInterface $aamcPcrs
     * @param array $parameters
     *
     * @return AamcPcrsInterface
     */
    public function put(
        AamcPcrsInterface $aamcPcrs,
        array $parameters
    ) {
        return $this->processForm(
            $aamcPcrs,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param AamcPcrsInterface $aamcPcrs
     * @param array $parameters
     *
     * @return AamcPcrsInterface
     */
    public function patch(
        AamcPcrsInterface $aamcPcrs,
        array $parameters
    ) {
        return $this->processForm(
            $aamcPcrs,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param AamcPcrsInterface $aamcPcrs
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return AamcPcrsInterface
     */
    protected function processForm(
        AamcPcrsInterface $aamcPcrs,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            AamcPcrsType::class,
            $aamcPcrs,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
