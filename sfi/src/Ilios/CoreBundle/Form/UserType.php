<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    const NAME = 'user';
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName')
            ->add('firstName')
            ->add('middleName')
            ->add('phone')
            ->add('email')
            ->add('addedViaIlios', 'checkbox', array(
                'label'     => 'Added via Ilios',
                'required'  => false,
            ))
            ->add('enabled', 'checkbox', array(
                'label'     => 'Enable this user?',
                'required'  => false,
            ))
            ->add('ucUid')
            ->add('otherId')
            ->add('examined', 'checkbox', array(
                'label'     => 'Examined?',
                'required'  => false,
            ))
            ->add('userSyncIgnore', 'checkbox', array(
                'label'     => 'User Sync Ignore?',
                'required'  => false,
            ))
            ->add('primarySchool')
            ->add('save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\User',
            'csrf_protection'   => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
