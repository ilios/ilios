<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\PurifiedTextareaType;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;

/**
 * Class LearningMaterialType
 * @package Ilios\CoreBundle\Form\Type
 */
class LearningMaterialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['empty_data' => null])
            ->add('description', PurifiedTextareaType::class)
            ->add('originalAuthor', null, ['required' => false, 'empty_data' => null])
            ->add('filename', null, ['empty_data' => null])
            ->add('copyrightPermission')
            ->add('copyrightRationale', null, ['empty_data' => null])
            ->add('filesize')
            ->add('mimetype', null, ['empty_data' => null])
            ->add('userRole', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearningMaterialUserRole"
            ])
            ->add('status', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearningMaterialStatus"
            ])
            ->add('owningUser', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('citation', TextType::class, ['required' => false, 'empty_data' => null])
            ->add('link', TextType::class, ['required' => false, 'empty_data' => null])
        ;
        $transformer = new RemoveMarkupTransformer();
        $elements = [
            'title',
            'originalAuthor',
            'filename',
            'copyrightRationale',
            'mimetype',
            'citation',
            'link',
        ];
        foreach ($elements as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
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
}
