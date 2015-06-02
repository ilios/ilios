<?php

namespace Ilios\CoreBundle\Form\Type;

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
            ->add('deleted', null, ['required' => false])
            ->add('lastUpdatedOn')
            ->add('session', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
            ->add('learnerGroups', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearnerGroup"
            ])
            ->add('publishEvent', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:PublishEvent"
            ])
            ->add('instructorGroups', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:InstructorGroup"
            ])
            ->add('users', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('recurringEvents', 'tdn_many_related', [
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
