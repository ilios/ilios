<?php

namespace Ilios\CoreBundle\Form\Type;

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
            ->add('additionalText', null, ['required' => false])
            ->add('dispatched', null, ['required' => false])
            ->add('changeTypes', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:AlertChangeType"
            ])
            ->add('instigators', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('recipients', 'tdn_many_related', [
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
