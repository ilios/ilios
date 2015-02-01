<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CurriculumInventoryExportType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('document')
            ->add('createdAt')
            ->add('report', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\CurriculumInventoryReport"])
            ->add('createdBy', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\User"])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\CurriculumInventoryExport'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_curriculuminventoryexport_form_type';
    }
}
