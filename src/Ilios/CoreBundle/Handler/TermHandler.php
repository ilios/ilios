<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\TermType;
use Ilios\CoreBundle\Entity\Manager\TermManager;
use Ilios\CoreBundle\Entity\TermInterface;

/**
 * Class TermHandler
 * @package Ilios\CoreBundle\Handler
 */
class TermHandler extends TermManager
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
     * @return TermInterface
     */
    public function post(array $parameters)
    {
        $term = $this->createTerm();

        return $this->processForm($term, $parameters, 'POST');
    }

    /**
     * @param TermInterface $term
     * @param array $parameters
     *
     * @return TermInterface
     */
    public function put(
        TermInterface $term,
        array $parameters
    ) {
        return $this->processForm(
            $term,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param TermInterface $term
     * @param array $parameters
     *
     * @return TermInterface
     */
    public function patch(
        TermInterface $term,
        array $parameters
    ) {
        return $this->processForm(
            $term,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param TermInterface $term
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return TermInterface
     */
    protected function processForm(
        TermInterface $term,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new TermType(),
            $term,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
