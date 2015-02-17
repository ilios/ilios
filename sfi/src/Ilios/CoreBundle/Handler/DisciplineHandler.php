<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\DisciplineType;
use Ilios\CoreBundle\Entity\Manager\DisciplineManager;
use Ilios\CoreBundle\Entity\DisciplineInterface;

class DisciplineHandler extends DisciplineManager
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
     * @return DisciplineInterface
     */
    public function post(array $parameters)
    {
        $discipline = $this->createDiscipline();

        return $this->processForm($discipline, $parameters, 'POST');
    }

    /**
     * @param DisciplineInterface $discipline
     * @param array $parameters
     *
     * @return DisciplineInterface
     */
    public function put(
        DisciplineInterface $discipline,
        array $parameters
    ) {
        return $this->processForm(
            $discipline,
            $parameters,
            'PUT'
        );
    }
    /**
     * @param DisciplineInterface $discipline
     * @param array $parameters
     *
     * @return DisciplineInterface
     */
    public function patch(
        DisciplineInterface $discipline,
        array $parameters
    ) {
        return $this->processForm(
            $discipline,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param DisciplineInterface $discipline
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return DisciplineInterface
     */
    protected function processForm(
        DisciplineInterface $discipline,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new DisciplineType(),
            $discipline,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $discipline = $form->getData();
            $this->updateDiscipline($discipline, true);

            return $discipline;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
