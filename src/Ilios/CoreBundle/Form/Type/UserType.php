<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType
 * @package Ilios\CoreBundle\Form\Type
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', null, ['empty_data' => null])
            ->add('firstName', null, ['empty_data' => null])
            ->add('middleName', null, ['required' => false, 'empty_data' => null])
            ->add('phone', null, ['required' => false, 'empty_data' => null])
            ->add('email', null, ['empty_data' => null])
            ->add('addedViaIlios', null, ['required' => false])
            ->add('enabled', null, ['required' => false])
            ->add('campusId', null, ['required' => false, 'empty_data' => null])
            ->add('icsFeedKey', null, ['empty_data' => null])
            ->add('otherId', null, ['required' => false, 'empty_data' => null])
            ->add('examined', null, ['required' => false])
            ->add('userSyncIgnore', null, ['required' => false])
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
            ->add('instructedLearnerGroups', 'tdn_many_related', [
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
            ->add('instructedOfferings', 'tdn_many_related', [
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
            ->add('cohorts', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Cohort"
            ])
            ->add('pendingUserUpdates', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:PendingUserUpdate"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        $textElements = ['firstName', 'lastName', 'middleName', 'phone', 'email', 'campusId', 'icsFeedKey', 'otherId'];
        foreach ($textElements as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
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
