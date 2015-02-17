<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecurringEventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('onSunday')
            ->add('onMonday')
            ->add('onTuesday')
            ->add('onWednesday')
            ->add('onThursday')
            ->add('onFriday')
            ->add('onSaturday')
            ->add('endDate')
            ->add('repetitionCount')
            ->add(
                'previousRecurringEvent',
                'tdn_entity',
                [
                    'required' => false,
                    'class' => "Ilios\\CoreBundle\\Entity\\RecurringEvent"
                ]
            )
            ->add(
                'nextRecurringEvent',
                'tdn_entity',
                [
                    'required' => false,
                    'class' => "Ilios\\CoreBundle\\Entity\\RecurringEvent"
                ]
            )
            ->add(
                'offerings',
                'tdn_entity',
                [
                    'required' => false,
                    'class' => "Ilios\\CoreBundle\\Entity\\Offering"
                ]
            )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\RecurringEvent'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_recurringevent_form_type';
    }
}
