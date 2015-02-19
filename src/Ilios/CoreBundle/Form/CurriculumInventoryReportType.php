<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CurriculumInventoryReportType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('year')
            ->add('startDate', 'datetime', array(
            'widget' => 'single_text',
            ))
            ->add('endDate', 'datetime', array(
            'widget' => 'single_text',
            ))
            ->add('export', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventoryExport"
            ])
            ->add('sequence', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventorySequence"
            ])
            ->add('program', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Program"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\CurriculumInventoryReport'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'curriculuminventoryreport';
    }
}
