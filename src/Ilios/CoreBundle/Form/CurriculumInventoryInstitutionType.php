<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CurriculumInventoryInstitutionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('aamcCode')
            ->add('streetAddress')
            ->add('city')
            ->add('state')
            ->add('zipCode')
            ->add('countryCode')
            ->add(
                'school',
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
            'data_class' => 'Ilios\CoreBundle\Entity\CurriculumInventoryInstitution'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_curriculuminventoryinstitution_form_type';
    }
}
