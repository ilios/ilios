<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\IlmSessionFacetType;
use Ilios\CoreBundle\Entity\Manager\IlmSessionManager;
use Ilios\CoreBundle\Entity\IlmSessionInterface;

/**
 * Class IlmSessionHandler
 * @package Ilios\CoreBundle\Handler
 */
class IlmSessionHandler extends IlmSessionManager
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
     * @return IlmSessionInterface
     */
    public function post(array $parameters)
    {
        $ilmSessionFacet = $this->createIlmSessionFacet();

        return $this->processForm($ilmSessionFacet, $parameters, 'POST');
    }

    /**
     * @param IlmSessionInterface $ilmSessionFacet
     * @param array $parameters
     *
     * @return IlmSessionInterface
     */
    public function put(
        IlmSessionInterface $ilmSessionFacet,
        array $parameters
    ) {
        return $this->processForm(
            $ilmSessionFacet,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param IlmSessionInterface $ilmSessionFacet
     * @param array $parameters
     *
     * @return IlmSessionInterface
     */
    public function patch(
        IlmSessionInterface $ilmSessionFacet,
        array $parameters
    ) {
        return $this->processForm(
            $ilmSessionFacet,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param IlmSessionInterface $ilmSessionFacet
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return IlmSessionInterface
     */
    protected function processForm(
        IlmSessionInterface $ilmSessionFacet,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new IlmSessionFacetType(),
            $ilmSessionFacet,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $ilmSessionFacet = $form->getData();
            $this->updateIlmSessionFacet(
                $ilmSessionFacet,
                true,
                ('PUT' === $method || 'PATCH' === $method)
            );

            return $ilmSessionFacet;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
