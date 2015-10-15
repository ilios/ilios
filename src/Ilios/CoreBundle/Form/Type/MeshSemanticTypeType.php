<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MeshSemanticTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('name')
            ->add('concepts', 'tdn_many_related', [
                'entityName' => "IliosCoreBundle:MeshConcept"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['id', 'name'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\MeshSemanticType'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'meshsemantictype';
    }
}
