<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Ilios\CoreBundle\Entity\Objective;
use Ilios\CoreBundle\Form\ObjectiveType;
use Ilios\CoreBundle\Exception\InvalidFormException;

class ObjectiveController extends FOSRestController
{

    /**
     * Get single objective,
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Objective for a given id",
     *   output = "Ilios\CoreBundle\Entity\Objective",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the objective is not found"
     *   }
     * )
     *
     * @param int     $id      the objective id
     *
     * @return Response
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function getObjectiveAction($id)
    {
        $objective = $this->container
                ->get('ilios_core.objective_handler')
                ->get($id);
        if (!$objective instanceof Objective) {
            throw new NotFoundHttpException(
                sprintf('The objective \'%s\' was not found.', $id)
            );
        }

        $view = $this->view(array('objective' => $objective), Codes::HTTP_OK)
                ->setTemplate("IliosCoreBundle:Objective:getObjective.html.twig")
                ->setTemplateVar('objective')
        ;

        return $this->handleView($view);
    }

    /**
     * List all objectives
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Lists all the objectives",
     *   output = "Ilios\CoreBundle\Entity\Objective"
     * )
     *
     * @return Response
     */
    public function getObjectivesAction()
    {
        $objectives = $this->container
                ->get('ilios_core.objective_handler')
                ->getAll();

        $view = $this->view(array('objectives' => $objectives), Codes::HTTP_OK)
                ->setTemplate("IliosCoreBundle:Objective:getObjectives.html.twig")
                ->setTemplateVar('objectives')
        ;

        return $this->handleView($view);
    }

    /**
     * Create an Objective from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new objective from the submitted data.",
     *   input = "Ilios\CoreBundle\Form\ObjectiveType",
     *   output = "Ilios\CoreBundle\Entity\Objective",
     *   statusCodes = {
     *     201 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @param Request $request the request object
     *
     * @return Response
     */
    public function postObjectiveAction(Request $request)
    {
        try {
            $obj = $this->container->get('ilios_core.objective_handler')->post(
                $request->request->get(ObjectiveType::NAME)
            );

            $view = $this->view(array('objective' => $obj), Codes::HTTP_CREATED)
                    ->setTemplate("IliosCoreBundle:Objective:getObjective.html.twig")
                    ->setTemplateVar('objective')
            ;

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {

            return $this->handleFormException($exception);
        }
    }

    /**
     * Presents the form to use to create a new objective.
     *
     * @return Response
     */
    public function newObjectiveAction()
    {
        $form = $this->createForm(
            new ObjectiveType(),
            null,
            array(
                'action' => $this->generateUrl('api_1_post_objective')
            )
        );
        $view = $this->view(array('form' => $form))
                ->setTemplate("IliosCoreBundle:Objective:newObjective.html.twig")
                ->setTemplateVar('form')
        ;

        return $this->handleView($view);
    }

    /**
     * Update existing objective from the submitted data or create a new objective
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Ilios\CoreBundle\Form\ObjectiveType",
     *   output = "Ilios\CoreBundle\Entity\Objective",
     *   statusCodes = {
     *     201 = "Returned when the Objective is created",
     *     202 = "Returned when updated",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the objective id
     *
     * @return Response
     *
     * @throws NotFoundHttpException when objective not exist
     */
    public function putObjectiveAction(Request $request, $id)
    {
        try {
            if (!($objective = $this->container->get('ilios_core.objective_handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $objective = $this->container->get('ilios_core.objective_handler')->post(
                    $request->request->get(ObjectiveType::NAME)
                );
            } else {
                $statusCode = Codes::HTTP_ACCEPTED;
                $handler = $this->container->get('ilios_core.objective_handler');
                $objective = $handler->put(
                    $objective,
                    $request->request->get(ObjectiveType::NAME)
                );
            }

            $view = $this->view(array('objective' => $objective), $statusCode)
                    ->setTemplate("IliosCoreBundle:Objective:getObjective.html.twig")
                    ->setTemplateVar('objective')
            ;

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            
            return $this->handleFormException($exception);
        }
    }
    
    /**
     * Generate a response for form validation errors
     * 
     * @param \Ilios\CoreBundle\Exception\InvalidFormException $exception
     * @return Response
     */
    protected function handleFormException(InvalidFormException $exception)
    {
        $form = $exception->getForm();
        $view = $this->view($form, Codes::HTTP_BAD_REQUEST)
            ->setTemplate("IliosCoreBundle:Objective:newObjective.html.twig")
            ->setTemplateVar('form')
        ;
        
        return $this->handleView($view);
    }
}
