<?php

namespace Ilios\CoreBundle\Form\Type\AbstractType;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as BaseEntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Ilios\CoreBundle\Form\DataTransformer\ArrayToIdTransformer;

class EntityType extends BaseEntityType
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $view_transformer = new ArrayToIdTransformer($this->em, null);
        $builder->addViewTransformer($view_transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'invalid_message' => 'The selected entity does not exist',
            )
        );
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'tdn_entity';
    }
}
