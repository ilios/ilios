<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompetencyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add(
                'school',
                'tdn_entity',
                [
                    'required' => false,
                    'class' => "Ilios\\CoreBundle\\Entity\\School"
                ]
            )
            ->add(
                'parent',
                'tdn_entity',
                [
                    'required' => false,
                    'class' => "Ilios\\CoreBundle\\Entity\\Competency"
                ]
            )
            ->add(
                'aamcPcrses',
                'tdn_entity',
                [
                    'required' => false,
                    'class' => "Ilios\\CoreBundle\\Entity\\AamcPcrs"
                ]
            )
            ->add(
                'programYears',
                'tdn_entity',
                [
                    'required' => false,
                    'class' => "Ilios\\CoreBundle\\Entity\\ProgramYear"
                ]
            )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Competency'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_competency_form_type';
    }
}
