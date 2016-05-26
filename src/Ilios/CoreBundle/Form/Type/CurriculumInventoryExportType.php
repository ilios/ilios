<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CurriculumInventoryExportType
 * @package Ilios\CoreBundle\Form\Type
 */
class CurriculumInventoryExportType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('report', SingleRelatedType::class, [
                'required' => true,
                'entityName' => "IliosCoreBundle:CurriculumInventoryReport"
            ])
            ->add('document', null, [ 'required' => false ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Ilios\CoreBundle\Entity\CurriculumInventoryExport'
            )
        );
    }
}
