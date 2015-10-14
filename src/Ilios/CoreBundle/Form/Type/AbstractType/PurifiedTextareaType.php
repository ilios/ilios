<?php

namespace Ilios\CoreBundle\Form\Type\AbstractType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PurifiedTextareaType
 * @package Ilios\CoreBundle\Form\Type\AbstractType
 */
class PurifiedTextareaType extends AbstractType
{
    /**
     * @var DataTransformerInterface
     */
    private $purifierTransformer;

    /**
     * @param DataTransformerInterface $purifierTransformer
     */
    public function __construct(DataTransformerInterface $purifierTransformer)
    {
        $this->purifierTransformer = $purifierTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer($this->purifierTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'compound' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'purified_textarea';
    }
}