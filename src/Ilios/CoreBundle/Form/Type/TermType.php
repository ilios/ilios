<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TermType
 * @package Ilios\CoreBundle\Form\Type
 */
class TermType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['required' => false, 'empty_data' => null])
            ->add('vocabulary', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Vocabulary"
            ])
            ->add('description', 'textarea', ['required' => false])
            ->add('parent', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Term"
            ])
            ->add('courses', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('programYears', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:ProgramYear"
            ])
            ->add('sessions', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
        ;

        $transformer = new RemoveMarkupTransformer();
        foreach (['title', 'description'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Term'
        ));
    }
}
