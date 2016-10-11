<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
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
            ->add('root', null, ['required' => false])
            ->add('school', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('directedCourses', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('learnerGroups', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearnerGroup"
            ])
            ->add('instructedLearnerGroups', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearnerGroup"
            ])
            ->add('instructorGroups', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:InstructorGroup"
            ])
            ->add('instructorIlmSessions', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:IlmSession"
            ])
            ->add('learnerIlmSessions', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:IlmSession"
            ])
            ->add('offerings', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Offering"
            ])
            ->add('instructedOfferings', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Offering"
            ])
            ->add('programYears', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:ProgramYear"
            ])
            ->add('roles', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:UserRole"
            ])
            ->add('primaryCohort', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Cohort"
            ])
            ->add('cohorts', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Cohort"
            ])
            ->add('authentication', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Authentication"
            ])
            ->add('directedSchools', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('administeredSchools', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('administeredSessions', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
            ->add('administeredCourses', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('directedPrograms', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Program"
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
}
