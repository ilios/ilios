<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CurriculumInventorySequenceBlockSessionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('countOfferingsOnce')
            ->add('sequenceBlock', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\CurriculumInventorySequenceBlock"])
            ->add('session', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Session"])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_curriculuminventorysequenceblocksession_form_type';
    }
}
