<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AamcResourceTypeType
 * @package Ilios\CoreBundle\Form\Type
 */
class AamcResourceTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('title', null, ['empty_data' => null])
            ->add('description', null, ['empty_data' => null])
            ->add('terms', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Term"
            ])
        ;

        $transformer = new RemoveMarkupTransformer();
        foreach (['title', 'description'] as $field) {
            $builder->get($field)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\AamcResourceType'
        ));
    }
}
