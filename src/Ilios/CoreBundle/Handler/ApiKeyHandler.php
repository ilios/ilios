<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\ApiKeyType;
use Ilios\CoreBundle\Entity\Manager\ApiKeyManager;
use Ilios\CoreBundle\Entity\ApiKeyInterface;

class ApiKeyHandler extends ApiKeyManager
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
     * @return ApiKeyInterface
     */
    public function post(array $parameters)
    {
        $apiKey = $this->createApiKey();

        return $this->processForm($apiKey, $parameters, 'POST');
    }

    /**
     * @param ApiKeyInterface $apiKey
     * @param array $parameters
     *
     * @return ApiKeyInterface
     */
    public function put(ApiKeyInterface $apiKey, array $parameters)
    {
        return $this->processForm($apiKey, $parameters, 'PUT');
    }

    /**
     * @param ApiKeyInterface $apiKey
     * @param array $parameters
     *
     * @return ApiKeyInterface
     */
    public function patch(ApiKeyInterface $apiKey, array $parameters)
    {
        return $this->processForm($apiKey, $parameters, 'PATCH');
    }

    /**
     * @param ApiKeyInterface $apiKey
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return ApiKeyInterface
     */
    protected function processForm(ApiKeyInterface $apiKey, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new ApiKeyType(), $apiKey, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
