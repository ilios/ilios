<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
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
            ->add('curriculumInventoryInstitution', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventoryInstitution"
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

    /**
     * @return string
     */
    public function getName()
    {
        return 'school';
    }
}
