<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IlmSessionFacetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hours')
            ->add('dueDate', 'datetime', array(
            'widget' => 'single_text',
            ))
            ->add('learnerGroups', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearnerGroup"
            ])
            ->add('instructorGroups', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:InstructorGroup"
            ])
            ->add('instructors', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('learners', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\IlmSessionFacet'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilmsessionfacet';
    }
}
