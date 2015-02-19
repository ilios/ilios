<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OfferingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('room')
            ->add('startDate', 'datetime', array(
            'widget' => 'single_text',
            ))
            ->add('endDate', 'datetime', array(
            'widget' => 'single_text',
            ))
            ->add('deleted')
            ->add('lastUpdatedOn')
            ->add('session', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
            ->add('learnerGroups', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearnerGroup"
            ])
            ->add('publishEvent', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:PublishEvent"
            ])
            ->add('instructorGroups', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:InstructorGroup"
            ])
            ->add('users', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('recurringEvents', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:RecurringEvent"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Offering'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'offering';
    }
}
