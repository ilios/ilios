<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SessionTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('sessionTypeCssClass')
            ->add('assessment')
            ->add('assessmentOption', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:AssessmentOption"
            ])
            ->add('owningSchool', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('aamcMethods', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:AamcMethod"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\SessionType'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sessiontype';
    }
}
