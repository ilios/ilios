<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
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
            ->add('vocabulary', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Vocabulary"
            ])
            ->add('description', 'purified_textarea', ['required' => false])
            ->add('parent', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Term"
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
            'data_class' => 'Ilios\CoreBundle\Entity\Term'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'term';
    }
}
