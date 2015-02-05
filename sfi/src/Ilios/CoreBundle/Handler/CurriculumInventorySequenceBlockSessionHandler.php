<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\CurriculumInventorySequenceBlockSessionType;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventorySequenceBlockSessionManager;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;

class CurriculumInventorySequenceBlockSessionHandler extends CurriculumInventorySequenceBlockSessionManager
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
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function post(array $parameters)
    {
        $curriculumInventorySequenceBlockSession = $this->createCurriculumInventorySequenceBlockSession();

        return $this->processForm($curriculumInventorySequenceBlockSession, $parameters, 'POST');
    }

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     * @param array $parameters
     *
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function put(CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession, array $parameters)
    {
        return $this->processForm($curriculumInventorySequenceBlockSession, $parameters, 'PUT');
    }

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     * @param array $parameters
     *
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function patch(CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession, array $parameters)
    {
        return $this->processForm($curriculumInventorySequenceBlockSession, $parameters, 'PATCH');
    }

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    protected function processForm(CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new CurriculumInventorySequenceBlockSessionType(), $curriculumInventorySequenceBlockSession, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $curriculumInventorySequenceBlockSession = $form->getData();
            $this->updateCurriculumInventorySequenceBlockSession($curriculumInventorySequenceBlockSession, true);

            return $curriculumInventorySequenceBlockSession;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
