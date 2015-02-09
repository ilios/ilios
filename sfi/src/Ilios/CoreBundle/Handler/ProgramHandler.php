<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\ProgramType;
use Ilios\CoreBundle\Entity\Manager\ProgramManager;
use Ilios\CoreBundle\Entity\ProgramInterface;

class ProgramHandler extends ProgramManager
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
     * @return ProgramInterface
     */
    public function post(array $parameters)
    {
        $program = $this->createProgram();

        return $this->processForm($program, $parameters, 'POST');
    }

    /**
     * @param ProgramInterface $program
     * @param array $parameters
     *
     * @return ProgramInterface
     */
    public function put(
        ProgramInterface $program,
        array $parameters
    ) {
        return $this->processForm(
            $program,
            $parameters,
            'PUT'
        );
    }
    /**
     * @param ProgramInterface $program
     * @param array $parameters
     *
     * @return ProgramInterface
     */
    public function patch(
        ProgramInterface $program,
        array $parameters
    ) {
        return $this->processForm(
            $program,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param ProgramInterface $program
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return ProgramInterface
     */
    protected function processForm(
        ProgramInterface $program,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new ProgramType(),
            $program,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $program = $form->getData();
            $this->updateProgram($program, true);

            return $program;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
