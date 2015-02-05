<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\ProgramYearType;
use Ilios\CoreBundle\Entity\Manager\ProgramYearManager;
use Ilios\CoreBundle\Entity\ProgramYearInterface;

class ProgramYearHandler extends ProgramYearManager
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param EntityManager $em
     * @param string $class
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManager $em, $class, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        parent::__construct($em, $class);
    }

    /**
     * @param array $parameters
     *
     * @return ProgramYearInterface
     */
    public function post(array $parameters)
    {
        $programYear = $this->createProgramYear();

        return $this->processForm($programYear, $parameters, 'POST');
    }

    /**
     * @param ProgramYearInterface $programYear
     * @param array $parameters
     *
     * @return ProgramYearInterface
     */
    public function put(ProgramYearInterface $programYear, array $parameters)
    {
        return $this->processForm($programYear, $parameters, 'PUT');
    }

    /**
     * @param ProgramYearInterface $programYear
     * @param array $parameters
     *
     * @return ProgramYearInterface
     */
    public function patch(ProgramYearInterface $programYear, array $parameters)
    {
        return $this->processForm($programYear, $parameters, 'PATCH');
    }

    /**
     * @param ProgramYearInterface $programYear
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return ProgramYearInterface
     */
    protected function processForm(ProgramYearInterface $programYear, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new ProgramYearType(), $programYear, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $programYear = $form->getData();
            $this->updateProgramYear($programYear, true);

            return $programYear;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
