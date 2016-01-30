<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MeshTermType
 * @package Ilios\CoreBundle\Form\Type
 */
class MeshTermType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, ['empty_data' => null])
            ->add('meshTermUid', null, ['empty_data' => null])
            ->add('lexicalTag', null, ['empty_data' => null])
            ->add('conceptPreferred')
            ->add('recordPreferred')
            ->add('permuted')
            ->add('printable')
            ->add('concepts', ManyRelatedType::class, [
                'entityName' => "IliosCoreBundle:MeshConcept"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['name', 'meshTermUid', 'lexicalTag'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\MeshTerm'
        ));
    }
}
