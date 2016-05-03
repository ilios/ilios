<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\AamcMethodType;
use Ilios\CoreBundle\Entity\Manager\AamcMethodManager;
use Ilios\CoreBundle\Entity\AamcMethodInterface;

/**
 * Class AamcMethodHandler
 * @package Ilios\CoreBundle\Handler
 */
class AamcMethodHandler extends AamcMethodManager
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
     * @return AamcMethodInterface
     */
    public function post(array $parameters)
    {
        $aamcMethod = $this->createAamcMethod();

        return $this->processForm($aamcMethod, $parameters, 'POST');
    }

    /**
     * @param AamcMethodInterface $aamcMethod
     * @param array $parameters
     *
     * @return AamcMethodInterface
     */
    public function put(
        AamcMethodInterface $aamcMethod,
        array $parameters
    ) {
        return $this->processForm(
            $aamcMethod,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param AamcMethodInterface $aamcMethod
     * @param array $parameters
     *
     * @return AamcMethodInterface
     */
    public function patch(
        AamcMethodInterface $aamcMethod,
        array $parameters
    ) {
        return $this->processForm(
            $aamcMethod,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param AamcMethodInterface $aamcMethod
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return AamcMethodInterface
     */
    protected function processForm(
        AamcMethodInterface $aamcMethod,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            AamcMethodType::class,
            $aamcMethod,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
