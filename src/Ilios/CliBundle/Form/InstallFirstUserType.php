<?php

namespace Ilios\CliBundle\Form;

use Ilios\CoreBundle\Entity\Manager\SchoolManagerInterface;
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
     * @var SchoolManagerInterface
     */
    protected $schoolManager;

    /**
     * @param SchoolManagerInterface $schoolManager
     */
    public function __construct(SchoolManagerInterface $schoolManager)
    {
        $this->schoolManager = $schoolManager;
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
                    'choices' => $this->getSchools(),
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ilios_install_first_user';
    }

    /**
     * Returns an associative array of all schools,
     * using school ids as keys and school names as values.
     *
     * @return array
     */
    protected function getSchools()
    {
        $entities = $this->schoolManager->findSchoolsBy([]);
        $schools = [];
        foreach ($entities as $entity) {
            $schools[$entity->getId()] = $entity->getTitle();
        }
        return $schools;
    }
}
