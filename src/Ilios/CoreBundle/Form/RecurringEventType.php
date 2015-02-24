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
            ->add('onSunday', null, ['required' => false])
            ->add('onMonday', null, ['required' => false])
            ->add('onTuesday', null, ['required' => false])
            ->add('onWednesday', null, ['required' => false])
            ->add('onThursday', null, ['required' => false])
            ->add('onFriday', null, ['required' => false])
            ->add('onSaturday', null, ['required' => false])
            ->add('endDate')
            ->add('repetitionCount', null, ['required' => false])
            ->add('previousRecurringEvent', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:RecurringEvent"
            ])
            ->add('nextRecurringEvent', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:RecurringEvent"
            ])
            ->add('offerings', 'tdn_many_related', [
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
