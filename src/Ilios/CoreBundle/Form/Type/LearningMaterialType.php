<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LearningMaterialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('originalAuthor', null, ['required' => false])
            ->add('userRole', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearningMaterialUserRole"
            ])
            ->add('status', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearningMaterialStatus"
            ])
            ->add('owningUser', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('citation', 'text', [
                    'required' => false
            ])
            ->add('sessionLearningMaterials', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionLearningMaterial"
            ])
            ->add('courseLearningMaterials', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CourseLearningMaterial"
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\LearningMaterial',
            // use a closure to determine which Validation Group applies
            // see http://symfony.com/doc/current/book/forms.html#groups-based-on-the-submitted-data
            'validation_groups' => function (FormInterface $form) {
                /**
                 * @var LearningMaterialInterface $data
                 */
                $data = $form->getData();

                if ('' !== trim($data->getCitation())) {
                    return array('Default', 'citation');
                } elseif ('' !== trim($data->getLink())) {
                    return array('Default', 'link');
                }
                return array('Default', 'file');
            },
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'learningmaterial';
    }
}
