<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProgramYearType
 * @package Ilios\CoreBundle\Form\Type
 */
class ProgramYearType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startYear')
            ->add('locked', null, ['required' => false])
            ->add('archived', null, ['required' => false])
            ->add('publishedAsTbd', null, ['required' => false])
            ->add('published', null, ['required' => false])
            ->add('program', SingleRelatedType::class, [
                'required' => true,
                'entityName' => "IliosCoreBundle:Program"
            ])
            ->add('cohort', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Cohort"
            ])
            ->add('directors', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('competencies', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Competency"
            ])
            ->add('terms', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Term"
            ])
            ->add('objectives', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\ProgramYear'
        ));
    }
}
