<?php

namespace Ilios\CoreBundle\Form\Type\AbstractType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Form\DataTransformer\ManyRelatedTransformer;
use Ilios\CoreBundle\Form\DataTransformer\ArrayToStringTransformer;

/**
 * Class ManyRelatedType
 * @package Ilios\CoreBundle\Form\Type\AbstractType
 */
class ManyRelatedType extends AbstractType
{
    /**
     * @var Registry
     */
    private $doctrineRegistry;

    /**
     * @param Registry $doctrineRegistry
     */
    public function __construct(Registry $doctrineRegistry)
    {
        $this->doctrineRegistry = $doctrineRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ManyRelatedTransformer($this->doctrineRegistry, $options['entityName']);
        $viewTransformer = new ArrayToStringTransformer();
        $builder->addModelTransformer($transformer);
        $builder->addViewTransformer($viewTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        if ($resolver instanceof OptionsResolver) {
            $resolver->setDefined('entityName');
            $resolver->setDefault('invalid_message', function (Options $options) {
                return 'This value is not valid.  Unable to find ' . $options['entityName'] . ' in the database.';
            });
        }

        $resolver->setRequired('entityName');
        $resolver->setAllowedTypes('entityName', 'string');
    }

    public function getParent()
    {
        return 'text';
    }
}
