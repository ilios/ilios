<?php

namespace Ilios\CoreBundle\Form\Type\AbstractType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Doctrine\Bundle\DoctrineBundle\Registry;

use Ilios\CoreBundle\Form\DataTransformer\SingleRelatedTransformer;

/**
 * Class SingleRelatedType
 * @package Ilios\CoreBundle\Form\Type\AbstractType
 */
class SingleRelatedType extends AbstractType
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
        $transformer = new SingleRelatedTransformer($this->doctrineRegistry, $options['entityName']);
        $builder->addModelTransformer($transformer);
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('entityName');
        $resolver->setRequired('entityName');
        $resolver->setAllowedTypes('entityName', 'string');

        $resolver->setDefault('invalid_message', function (Options $options) {
            return 'This value is not valid.  Unable to find ' . $options['entityName'] . ' in the database.';
        });
    }

    public function getParent()
    {
        return 'text';
    }
}
