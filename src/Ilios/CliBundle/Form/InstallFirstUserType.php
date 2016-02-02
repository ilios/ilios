<?php

namespace Ilios\CliBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Input form for the "create first user" command.
 *
 * Class InstallFirstUserType
 * @package Ilios\CliBundle\Form
 */
class InstallFirstUserType extends AbstractType
{
    /**
     * @var array A map of school ids/titles.
     */
    protected $schools;

    /**
     * @param array $schools An map of school id/titles.
     */
    public function __construct(array $schools)
    {
        $this->schools = $schools;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'school',
                'choice',
                [
                    'label' => 'Select a school',
                    'choices' => $this->schools,
                    'required' => true,
                ]
            )
            ->add(
                'email',
                'email',
                [
                    'label' => 'Enter an email address',
                    'required' => true,
                    'constraints' => [
                        new Email()
                    ]
                ]
            );
    }
}
