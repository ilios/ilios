<?php

namespace Ilios\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Doctrine\Common\Persistence\ObjectManager;

use Ilios\CoreBundle\Form\Transformer\SingleRelatedTransformer;

class SingleRelatedType extends AbstractType
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

        $transformer = new SingleRelatedTransformer($this->om, $options['entityName']);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefined('entityName');
        $resolver->setRequired('entityName');
        $resolver->setAllowedTypes('entityName', 'string');

        $resolver->setDefault('invalid_message', function (Options $options) {
            return 'This value is not valid.  Unable to find ' . $options['entityName'] . ' in the database.';
        });
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'single_related';
    }
}
