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
            ->add('endDate', 'datetime', array(
            'widget' => 'single_text',
            ))
            ->add('repetitionCount')
            ->add('previousRecurringEvent', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:RecurringEvent"
            ])
            ->add('nextRecurringEvent', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:RecurringEvent"
            ])
            ->add('offerings', 'multi_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Offering"
            ])
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
        return 'recurringevent';
    }
}
