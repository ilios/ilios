<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class IlmSessionType
 * @package Ilios\CoreBundle\Form\Type
 */
class IlmSessionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hours')
            ->add('dueDate', DateTimeType::class, array(
                'widget' => 'single_text',
            ))
            ->add('learnerGroups', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearnerGroup"
            ])
            ->add('instructorGroups', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:InstructorGroup"
            ])
            ->add('instructors', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('learners', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('session', SingleRelatedType::class, [
                'required' => true,
                'entityName' => "IliosCoreBundle:Session"
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\IlmSession'
        ));
    }
}
