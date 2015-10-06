<?php

namespace Ilios\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SchoolType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('templatePrefix', null, ['required' => false])
            ->add('iliosAdministratorEmail')
            ->add('deleted', null, ['required' => false])
            ->add('changeAlertRecipients', null, ['required' => false])
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
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
