<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('concepts', 'tdn_many_related', [
                'entityName' => "IliosCoreBundle:MeshConcept"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['name', 'meshTermUid', 'lexicalTag'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\MeshTerm'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'meshterm';
    }
}
