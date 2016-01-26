<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SessionType
 * @package Ilios\CoreBundle\Form\Type
 */
class SessionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['required' => false, 'empty_data' => null])
            ->add('attireRequired', null, ['required' => false])
            ->add('equipmentRequired', null, ['required' => false])
            ->add('supplemental', null, ['required' => false])
            ->add('publishedAsTbd', null, ['required' => false])
            ->add('published', null, ['required' => false])
            ->add('sessionType', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionType"
            ])
            ->add('course', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add(
                'ilmSession',
                'tdn_single_related',
                [
                'required' => false,
                'entityName' => "IliosCoreBundle:IlmSession"
                ]
            )
            ->add('topics', 'tdn_many_related', [
            'required' => false,
            'entityName' => "IliosCoreBundle:Topic"
            ])
            ->add('terms', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Term"
            ])
            ->add('objectives', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('meshDescriptors', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshDescriptor"
            ])
            ->add('sessionDescription', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionDescription"
            ])
        ;

        $builder->get('title')->addViewTransformer(new RemoveMarkupTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Session'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'session';
    }
}
