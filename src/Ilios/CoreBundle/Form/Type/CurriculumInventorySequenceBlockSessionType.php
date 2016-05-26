<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CurriculumInventorySequenceBlockSessionType
 * @package Ilios\CoreBundle\Form\Type
 */
class CurriculumInventorySequenceBlockSessionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('countOfferingsOnce', null, ['required' => false])
            ->add('sequenceBlock', SingleRelatedType::class, [
                'required' => true,
                'entityName' => "IliosCoreBundle:CurriculumInventorySequenceBlock"
            ])
            ->add('session', SingleRelatedType::class, [
                'required' => true,
                'entityName' => "IliosCoreBundle:Session"
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession'
        ));
    }
}
