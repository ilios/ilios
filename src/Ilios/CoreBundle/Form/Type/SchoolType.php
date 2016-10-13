<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SchoolType
 * @package Ilios\CoreBundle\Form\Type
 */
class SchoolType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['empty_data' => null])
            ->add('templatePrefix', null, ['required' => false, 'empty_data' => null])
            ->add('iliosAdministratorEmail', null, ['empty_data' => null])
            ->add('changeAlertRecipients', null, ['required' => false, 'empty_data' => null])
            ->add('curriculumInventoryInstitution', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventoryInstitution"
            ])
            ->add('directors', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('administrators', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['title', 'templatePrefix', 'iliosAdministratorEmail', 'changeAlertRecipients'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\School'
        ));
    }
}
