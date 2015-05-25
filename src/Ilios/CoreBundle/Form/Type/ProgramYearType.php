<?php

namespace Ilios\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProgramYearType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startYear')
            ->add('deleted', null, ['required' => false])
            ->add('locked', null, ['required' => false])
            ->add('archived', null, ['required' => false])
            ->add('publishedAsTbd', null, ['required' => false])
            ->add('program', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Program"
            ])
            ->add('cohort', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Cohort"
            ])
            ->add('directors', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('competencies', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Competency"
            ])
            ->add('disciplines', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Discipline"
            ])
            ->add('objectives', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('publishEvent', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:PublishEvent"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\ProgramYear'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'programyear';
    }
}
