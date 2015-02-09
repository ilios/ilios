<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName')
            ->add('firstName')
            ->add('middleName')
            ->add('phone')
            ->add('email')
            ->add('addedViaIlios')
            ->add('enabled')
            ->add('ucUid')
            ->add('otherId')
            ->add('examined')
            ->add('userSyncIgnore')
            ->add('apiKey', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\ApiKey"])
            ->add('primarySchool', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\School"])
            ->add('directedCourses', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Course"])
            ->add('learnerGroups', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\LearnerGroup"])
            ->add('instructorUserGroups', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\LearnerGroup"])
            ->add('instructorGroups', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\InstructorGroup"])
            ->add('instructorIlmSessions', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\IlmSessionFacet"])
            ->add('learnerIlmSessions', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\IlmSessionFacet"])
            ->add('offerings', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Offering"])
            ->add('programYears', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\ProgramYear"])
            ->add('alerts', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Alert"])
            ->add('roles', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\UserRole"])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_user_form_type';
    }
}
