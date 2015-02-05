<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\SessionType;
use Ilios\CoreBundle\Entity\Manager\SessionManager;
use Ilios\CoreBundle\Entity\SessionInterface;

class SessionHandler extends SessionManager
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
     * @return SessionInterface
     */
    public function post(array $parameters)
    {
        $session = $this->createSession();

        return $this->processForm($session, $parameters, 'POST');
    }

    /**
     * @param SessionInterface $session
     * @param array $parameters
     *
     * @return SessionInterface
     */
    public function put(SessionInterface $session, array $parameters)
    {
        return $this->processForm($session, $parameters, 'PUT');
    }

    /**
     * @param SessionInterface $session
     * @param array $parameters
     *
     * @return SessionInterface
     */
    public function patch(SessionInterface $session, array $parameters)
    {
        return $this->processForm($session, $parameters, 'PATCH');
    }

    /**
     * @param SessionInterface $session
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return SessionInterface
     */
    protected function processForm(SessionInterface $session, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new SessionType(), $session, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $session = $form->getData();
            $this->updateSession($session, true);

            return $session;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
