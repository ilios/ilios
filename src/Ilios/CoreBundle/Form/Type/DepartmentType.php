<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DepartmentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('deleted', null, ['required' => false])
            ->add('school', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('stewards', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:ProgramYearSteward"
            ])
        ;
        $builder->get('title')->addViewTransformer(new RemoveMarkupTransformer());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Department'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'department';
    }
}
