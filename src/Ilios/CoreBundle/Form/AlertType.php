<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AlertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tableRowId')
            ->add('tableName')
            ->add('additionalText')
            ->add('dispatched')
            ->add('changeTypes', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:AlertChangeType"
            ])
            ->add('instigators', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('recipients', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Alert'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'alert';
    }
}
