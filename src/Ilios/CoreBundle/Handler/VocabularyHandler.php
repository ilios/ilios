<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\VocabularyType;
use Ilios\CoreBundle\Entity\Manager\VocabularyManager;
use Ilios\CoreBundle\Entity\VocabularyInterface;

/**
 * Class VocabularyHandler
 * @package Ilios\CoreBundle\Handler
 */
class VocabularyHandler extends VocabularyManager
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
     * @return VocabularyInterface
     */
    public function post(array $parameters)
    {
        $vocabulary = $this->createVocabulary();

        return $this->processForm($vocabulary, $parameters, 'POST');
    }

    /**
     * @param VocabularyInterface $vocabulary
     * @param array $parameters
     *
     * @return VocabularyInterface
     */
    public function put(
        VocabularyInterface $vocabulary,
        array $parameters
    ) {
        return $this->processForm(
            $vocabulary,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param VocabularyInterface $vocabulary
     * @param array $parameters
     *
     * @return VocabularyInterface
     */
    public function patch(
        VocabularyInterface $vocabulary,
        array $parameters
    ) {
        return $this->processForm(
            $vocabulary,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param VocabularyInterface $vocabulary
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return VocabularyInterface
     */
    protected function processForm(
        VocabularyInterface $vocabulary,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            VocabularyType::class,
            $vocabulary,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
