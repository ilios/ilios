<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\IlmSessionFacetType;
use Ilios\CoreBundle\Entity\Manager\IlmSessionFacetManager;
use Ilios\CoreBundle\Entity\IlmSessionFacetInterface;

/**
 * Class IlmSessionFacetHandler
 * @package Ilios\CoreBundle\Handler
 */
class IlmSessionFacetHandler extends IlmSessionFacetManager
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
     * @return IlmSessionFacetInterface
     */
    public function post(array $parameters)
    {
        $ilmSessionFacet = $this->createIlmSessionFacet();

        return $this->processForm($ilmSessionFacet, $parameters, 'POST');
    }

    /**
     * @param IlmSessionFacetInterface $ilmSessionFacet
     * @param array $parameters
     *
     * @return IlmSessionFacetInterface
     */
    public function put(
        IlmSessionFacetInterface $ilmSessionFacet,
        array $parameters
    ) {
        return $this->processForm(
            $ilmSessionFacet,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param IlmSessionFacetInterface $ilmSessionFacet
     * @param array $parameters
     *
     * @return IlmSessionFacetInterface
     */
    public function patch(
        IlmSessionFacetInterface $ilmSessionFacet,
        array $parameters
    ) {
        return $this->processForm(
            $ilmSessionFacet,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param IlmSessionFacetInterface $ilmSessionFacet
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return IlmSessionFacetInterface
     */
    protected function processForm(
        IlmSessionFacetInterface $ilmSessionFacet,
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
