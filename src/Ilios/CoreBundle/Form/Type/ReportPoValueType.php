<?php

namespace Ilios\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReportPoValueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prepositionalObjectTableRowId')
            ->add('deleted', null, ['required' => false])
            ->add('report', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Report"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\ReportPoValue'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reportpovalue';
    }
}
