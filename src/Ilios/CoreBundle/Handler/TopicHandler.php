<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\TopicType;
use Ilios\CoreBundle\Entity\Manager\TopicManager;
use Ilios\CoreBundle\Entity\TopicInterface;

/**
 * @deprecated
 * Class TopicHandler
 * @package Ilios\CoreBundle\Handler
 */
class TopicHandler extends TopicManager
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
     * @return TopicInterface
     */
    public function post(array $parameters)
    {
        $topic = $this->createTopic();

        return $this->processForm($topic, $parameters, 'POST');
    }

    /**
     * @param TopicInterface $topic
     * @param array $parameters
     *
     * @return TopicInterface
     */
    public function put(
        TopicInterface $topic,
        array $parameters
    ) {
        return $this->processForm(
            $topic,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param TopicInterface $topic
     * @param array $parameters
     *
     * @return TopicInterface
     */
    public function patch(
        TopicInterface $topic,
        array $parameters
    ) {
        return $this->processForm(
            $topic,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param TopicInterface $topic
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return TopicInterface
     */
    protected function processForm(
        TopicInterface $topic,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new TopicType(),
            $topic,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
