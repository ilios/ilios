<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PermissionType
 * @package Ilios\CoreBundle\Form\Type
 *
 * @deprecated
 */
class PermissionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('canRead', null)
            ->add('canWrite', null)
            ->add('tableRowId', null)
            ->add('tableName', null)
            ->add('user', SingleRelatedType::class, [
                'required' => true,
                'entityName' => "IliosCoreBundle:User"
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Permission'
        ));
    }
}
