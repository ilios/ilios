<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AlertType
 * @package Ilios\CoreBundle\Form\Type
 */
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
            ->add('tableName', null, ['empty_data' => null])
            ->add('additionalText', null, ['required' => false, 'empty_data' => null])
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
        $transformer = new RemoveMarkupTransformer();
        foreach (['tableName', 'additionalText'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Alert',
        ));
    }
}
