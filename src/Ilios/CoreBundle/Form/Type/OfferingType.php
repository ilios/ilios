<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class OfferingType
 * @package Ilios\CoreBundle\Form\Type
 */
class OfferingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('room', null, ['empty_data' => null])
            ->add('site', null, ['required' => false, 'empty_data' => null])
            ->add('startDate', 'datetime', array(
                'widget' => 'single_text',
            ))
            ->add('endDate', 'datetime', array(
                'widget' => 'single_text',
            ))
            ->add('session', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
            ->add('learnerGroups', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearnerGroup"
            ])
            ->add('instructorGroups', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:InstructorGroup"
            ])
            ->add('learners', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('instructors', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['room', 'site'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Offering'
        ));
    }
}
