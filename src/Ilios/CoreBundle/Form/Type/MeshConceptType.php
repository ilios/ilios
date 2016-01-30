<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MeshConceptType
 * @package Ilios\CoreBundle\Form\Type
 */
class MeshConceptType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', null, ['empty_data' => null])
            ->add('name', null, ['empty_data' => null])
            ->add('umlsUid', null, ['empty_data' => null])
            ->add('preferred', null, ['required' => false])
            ->add('scopeNote', null, ['required' => false, 'empty_data' => null])
            ->add('casn1Name', null, ['required' => false, 'empty_data' => null])
            ->add('registryNumber', null, ['required' => false, 'empty_data' => null])
            ->add('descriptors', 'tdn_many_related', [
                'entityName' => "IliosCoreBundle:MeshDescriptor"
            ])
            ->add('semanticTypes', 'tdn_many_related', [
                'entityName' => "IliosCoreBundle:MeshSemanticType"
            ])
            ->add('terms', 'tdn_many_related', [
                'entityName' => "IliosCoreBundle:MeshTerm"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['id', 'name', 'umlsUid', 'scopeNote', 'casn1Name', 'registryNumber'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\MeshConcept'
        ));
    }
}
