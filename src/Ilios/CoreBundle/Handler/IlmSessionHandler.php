<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\IlmSessionType;
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
        $ilmSession = $this->createIlmSession();

        return $this->processForm($ilmSession, $parameters, 'POST');
    }

    /**
     * @param IlmSessionInterface $ilmSession
     * @param array $parameters
     *
     * @return IlmSessionInterface
     */
    public function put(
        IlmSessionInterface $ilmSession,
        array $parameters
    ) {
        return $this->processForm(
            $ilmSession,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param IlmSessionInterface $ilmSession
     * @param array $parameters
     *
     * @return IlmSessionInterface
     */
    public function patch(
        IlmSessionInterface $ilmSession,
        array $parameters
    ) {
        return $this->processForm(
            $ilmSession,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param IlmSessionInterface $ilmSession
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return IlmSessionInterface
     */
    protected function processForm(
        IlmSessionInterface $ilmSession,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            IlmSessionType::class,
            $ilmSession,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
