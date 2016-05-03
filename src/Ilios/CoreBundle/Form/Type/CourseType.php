<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

/**
 * Class CourseType
 * @package Ilios\CoreBundle\Form\Type
 */
class CourseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['required' => false, 'empty_data' => null])
            ->add('level')
            ->add('year')
            ->add('startDate', DateTimeType::class, array(
                'widget' => 'single_text',
            ))
            ->add('endDate', DateTimeType::class, array(
                'widget' => 'single_text',
            ))
            ->add('externalId', null, ['required' => false, 'empty_data' => null])
            ->add('locked', null, ['required' => false])
            ->add('archived', null, ['required' => false])
            ->add('publishedAsTbd', null, ['required' => false])
            ->add('published', null, ['required' => false])
            ->add('clerkshipType', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:CourseClerkshipType"
            ])
            ->add('school', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('directors', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('cohorts', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Cohort"
            ])
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
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['title', 'externalId'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Course'
        ));
    }
}
