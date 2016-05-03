<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserMadeReminderType
 * @package Ilios\CoreBundle\Form\Type
 */
class UserMadeReminderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('note', null, ['empty_data' => null])
            ->add('dueDate', DateTimeType::class, array(
                'widget' => 'single_text',
            ))
            ->add('closed', null, ['required' => false])
            ->add('user', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
        ;
        $builder->get('note')->addViewTransformer(new RemoveMarkupTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\UserMadeReminder'
        ));
    }
}
