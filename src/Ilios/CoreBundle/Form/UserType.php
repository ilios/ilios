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
            ->add('apiKey', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:ApiKey"
            ])
            ->add('primarySchool', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('directedCourses', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('learnerGroups', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearnerGroup"
            ])
            ->add('instructorUserGroups', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearnerGroup"
            ])
            ->add('instructorGroups', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:InstructorGroup"
            ])
            ->add('instructorIlmSessions', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:IlmSessionFacet"
            ])
            ->add('learnerIlmSessions', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:IlmSessionFacet"
            ])
            ->add('offerings', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Offering"
            ])
            ->add('programYears', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:ProgramYear"
            ])
            ->add('alerts', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Alert"
            ])
            ->add('roles', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:UserRole"
            ])
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
        return 'user';
    }
}
