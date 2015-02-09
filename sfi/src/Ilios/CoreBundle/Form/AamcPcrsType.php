<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AamcPcrsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('description')
            ->add('competencies', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Competency"])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\AamcPcrs'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_aamcpcrs_form_type';
    }
}
