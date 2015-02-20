<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProgramType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('shortTitle')
            ->add('duration')
            ->add('deleted')
            ->add('publishedAsTbd')
            ->add(
                'publishEvent',
                'tdn_entity',
                [
                    'required' => false,
                    'class' => "Ilios\\CoreBundle\\Entity\\PublishEvent"
                ]
            )
            ->add(
                'owningSchool',
                'tdn_entity',
                [
                    'required' => false,
                    'class' => "Ilios\\CoreBundle\\Entity\\School"
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
            'data_class' => 'Ilios\CoreBundle\Entity\Program'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_program_form_type';
    }
}
