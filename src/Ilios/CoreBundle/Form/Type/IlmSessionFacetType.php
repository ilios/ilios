<?php

namespace Ilios\CoreBundle\Form\Type;

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
            ->add('learnerGroups', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearnerGroup"
            ])
            ->add('instructorGroups', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:InstructorGroup"
            ])
            ->add('instructors', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('learners', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('session', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
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
