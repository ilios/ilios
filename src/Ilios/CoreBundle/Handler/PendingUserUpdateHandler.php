<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\PendingUserUpdateType;
use Ilios\CoreBundle\Entity\Manager\PendingUserUpdateManager;
use Ilios\CoreBundle\Entity\PendingUserUpdateInterface;

/**
 * Class PendingUserUpdateHandler
 * @package Ilios\CoreBundle\Handler
 */
class PendingUserUpdateHandler extends PendingUserUpdateManager
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
     * @return PendingUserUpdateInterface
     */
    public function post(array $parameters)
    {
        $pendingUserUpdate = $this->createPendingUserUpdate();

        return $this->processForm($pendingUserUpdate, $parameters, 'POST');
    }

    /**
     * @param PendingUserUpdateInterface $pendingUserUpdate
     * @param array $parameters
     *
     * @return PendingUserUpdateInterface
     */
    public function put(
        PendingUserUpdateInterface $pendingUserUpdate,
        array $parameters
    ) {
        return $this->processForm(
            $pendingUserUpdate,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param PendingUserUpdateInterface $pendingUserUpdate
     * @param array $parameters
     *
     * @return PendingUserUpdateInterface
     */
    public function patch(
        PendingUserUpdateInterface $pendingUserUpdate,
        array $parameters
    ) {
        return $this->processForm(
            $pendingUserUpdate,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param PendingUserUpdateInterface $pendingUserUpdate
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return PendingUserUpdateInterface
     */
    protected function processForm(
        PendingUserUpdateInterface $pendingUserUpdate,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            PendingUserUpdateType::class,
            $pendingUserUpdate,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
