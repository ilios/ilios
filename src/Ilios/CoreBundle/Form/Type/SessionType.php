<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
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
            ->add('sessionType', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionType"
            ])
            ->add('course', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add(
                'ilmSession',
                SingleRelatedType::class,
                [
                'required' => false,
                'entityName' => "IliosCoreBundle:IlmSession"
                ]
            )
            ->add('terms', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Term"
            ])
            ->add('objectives', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('meshDescriptors', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshDescriptor"
            ])
            ->add('sessionDescription', SingleRelatedType::class, [
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
}
