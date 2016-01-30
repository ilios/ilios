<?php

namespace Ilios\CoreBundle\Form\Type\AbstractType;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as BaseEntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ilios\CoreBundle\Form\DataTransformer\ArrayToIdTransformer;

/**
 * Class EntityType
 * @package Ilios\CoreBundle\Form\Type\AbstractType
 */
class EntityType extends BaseEntityType
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
        $view_transformer = new ArrayToIdTransformer($this->doctrineRegistry, null);
        $builder->addViewTransformer($view_transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'invalid_message' => 'The selected entity does not exist',
            )
        );
    }

    public function getParent()
    {
        return 'entity';
    }
}
