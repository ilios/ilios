<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CompetencyType
 * @package Ilios\CoreBundle\Form\Type
 */
class CompetencyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['required' => false, 'empty_data' => null])
            ->add('school', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('parent', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Competency"
            ])
            ->add('aamcPcrses', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:AamcPcrs"
            ])
            ->add('programYears', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:ProgramYear"
            ])
        ;
        $builder->get('title')->addViewTransformer(new RemoveMarkupTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Competency'
        ));
    }
}
