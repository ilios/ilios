<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProgramType
 * @package Ilios\CoreBundle\Form\Type
 */
class ProgramType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['empty_data' => null])
            ->add('shortTitle', null, ['required' => false, 'empty_data' => null])
            ->add('duration')
            ->add('publishedAsTbd', null, ['required' => false])
            ->add('published', null, ['required' => false])
            ->add('school', SingleRelatedType::class, [
                'required' => true,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('directors', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['title', 'shortTitle'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Program'
        ));
    }
}
