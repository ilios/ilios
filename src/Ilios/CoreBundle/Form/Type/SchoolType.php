<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SchoolType
 * @package Ilios\CoreBundle\Form\Type
 */
class SchoolType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['empty_data' => null])
            ->add('templatePrefix', null, ['required' => false, 'empty_data' => null])
            ->add('iliosAdministratorEmail', null, ['empty_data' => null])
            ->add('deleted', null, ['required' => false])
            ->add('changeAlertRecipients', null, ['required' => false, 'empty_data' => null])
            ->add('alerts', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Alert"
            ])
            ->add('competencies', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Competency"
            ])
            ->add('courses', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('programs', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Program"
            ])
            ->add('departments', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Department"
            ])
            ->add('topics', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Topic"
            ])
            ->add('instructorGroups', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:InstructorGroup"
            ])
            ->add('curriculumInventoryInstitution', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventoryInstitution"
            ])
            ->add('sessionTypes', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionType"
            ])
            ->add('stewards', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:ProgramYearSteward"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['title', 'templatePrefix', 'iliosAdministratorEmail', 'changeAlertRecipients'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\School'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'school';
    }
}
