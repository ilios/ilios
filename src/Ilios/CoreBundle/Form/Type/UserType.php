<?php

namespace Ilios\CoreBundle\Form\Type;

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
            ->add('middleName', null, ['required' => false])
            ->add('phone', null, ['required' => false])
            ->add('email')
            ->add('addedViaIlios', null, ['required' => false])
            ->add('enabled', null, ['required' => false])
            ->add('ucUid', null, ['required' => false])
            ->add('otherId', null, ['required' => false])
            ->add('examined', null, ['required' => false])
            ->add('userSyncIgnore', null, ['required' => false])
            ->add('apiKey', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:ApiKey"
            ])
            ->add('reminders', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:UserMadeReminder"
            ])
            ->add('learningMaterials', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearningMaterial"
            ])
            ->add('publishEvents', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:PublishEvent"
            ])
            ->add('reports', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Report"
            ])
            ->add('school', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('directedCourses', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('learnerGroups', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearnerGroup"
            ])
            ->add('instructorUserGroups', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearnerGroup"
            ])
            ->add('instructorGroups', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:InstructorGroup"
            ])
            ->add('instructorIlmSessions', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:IlmSession"
            ])
            ->add('learnerIlmSessions', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:IlmSession"
            ])
            ->add('offerings', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Offering"
            ])
            ->add('programYears', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:ProgramYear"
            ])
            ->add('alerts', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Alert"
            ])
            ->add('roles', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:UserRole"
            ])
            ->add('primaryCohort', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Cohort"
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
