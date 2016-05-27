<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProgramYearStewardType
 * @package Ilios\CoreBundle\Form\Type
 */
class ProgramYearStewardType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('department', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Department"
            ])
            ->add('programYear', SingleRelatedType::class, [
                'required' => true,
                'entityName' => "IliosCoreBundle:ProgramYear"
            ])
            ->add('school', SingleRelatedType::class, [
                'required' => true,
                'entityName' => "IliosCoreBundle:School"
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\ProgramYearSteward'
        ));
    }
}
