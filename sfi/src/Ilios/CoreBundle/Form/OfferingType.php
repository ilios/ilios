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
            ->add('startDate')
            ->add('endDate')
            ->add('deleted')
            ->add('lastUpdatedOn')
            ->add('session', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Session"])
            ->add('groups', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\LearnerGroup"])
            ->add('publishEvent', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\PublishEvent"])
            ->add('instructorGroups', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\InstructorGroup"])
            ->add('users', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\User"])
            ->add('recurringEvents', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\RecurringEvent"])
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
        return 'ilios_corebundle_offering_form_type';
    }
}
