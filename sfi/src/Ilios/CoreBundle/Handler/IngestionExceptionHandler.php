<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\IngestionExceptionType;
use Ilios\CoreBundle\Entity\Manager\IngestionExceptionManager;
use Ilios\CoreBundle\Entity\IngestionExceptionInterface;

class IngestionExceptionHandler extends IngestionExceptionManager
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
     * @return IngestionExceptionInterface
     */
    public function post(array $parameters)
    {
        $ingestionException = $this->createIngestionException();

        return $this->processForm($ingestionException, $parameters, 'POST');
    }

    /**
     * @param IngestionExceptionInterface $ingestionException
     * @param array $parameters
     *
     * @return IngestionExceptionInterface
     */
    public function put(
        IngestionExceptionInterface $ingestionException,
        array $parameters
    ) {
        return $this->processForm(
            $ingestionException,
            $parameters,
            'PUT'
        );
    }
    /**
     * @param IngestionExceptionInterface $ingestionException
     * @param array $parameters
     *
     * @return IngestionExceptionInterface
     */
    public function patch(
        IngestionExceptionInterface $ingestionException,
        array $parameters
    ) {
        return $this->processForm(
            $ingestionException,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param IngestionExceptionInterface $ingestionException
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return IngestionExceptionInterface
     */
    protected function processForm(
        IngestionExceptionInterface $ingestionException,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new IngestionExceptionType(),
            $ingestionException,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $ingestionException = $form->getData();
            $this->updateIngestionException($ingestionException, true);

            return $ingestionException;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
