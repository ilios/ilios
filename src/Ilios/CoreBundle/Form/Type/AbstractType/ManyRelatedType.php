<?php

namespace Ilios\CoreBundle\Form\Type\AbstractType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Doctrine\Common\Persistence\ObjectManager;
use Ilios\CoreBundle\Form\DataTransformer\ManyRelatedTransformer;
use Ilios\CoreBundle\Form\DataTransformer\ArrayToStringTransformer;

class ManyRelatedType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ManyRelatedTransformer($this->om, $options['entityName']);
        $viewTransformer = new ArrayToStringTransformer();
        $builder->addModelTransformer($transformer);
        $builder->addViewTransformer($viewTransformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        if ($resolver instanceof OptionsResolver) {
            $resolver->setDefined('entityName');
            $resolver->setDefault('invalid_message', function (Options $options) {
                return 'This value is not valid.  Unable to find ' . $options['entityName'] . ' in the database.';
            });
        }

        $resolver->setRequired('entityName');
        $resolver->setAllowedTypes('entityName', 'string');
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'tdn_many_related';
    }
}
