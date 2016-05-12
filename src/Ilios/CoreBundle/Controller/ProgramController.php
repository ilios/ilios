<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\ProgramHandler;
use Ilios\CoreBundle\Entity\ProgramInterface;

/**
 * Class ProgramController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Programs")
 */
class ProgramController extends FOSRestController
{
    /**
     * Get a Program
     *
     * @ApiDoc(
     *   section = "Program",
     *   description = "Get a Program.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Program identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Program",
     *   statusCodes={
     *     200 = "Program.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {
        $program = $this->getProgramHandler()->findProgramDTOBy(['id' => $id]);

        if (!$program) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $program)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['programs'][] = $program;

        return $answer;
    }

    /**
     * Get all Program.
     *
     * @ApiDoc(
     *   section = "Program",
     *   description = "Get all Program.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Program",
     *   statusCodes = {
     *     200 = "List of all Program",
     *     204 = "No content. Nothing to list."
     *   }
     * )
     *
     * @QueryParam(
     *   name="offset",
     *   requirements="\d+",
     *   nullable=true,
     *   description="Offset from which to start listing notes."
     * )
     * @QueryParam(
     *   name="limit",
     *   requirements="\d+",
     *   default="20",
     *   description="How many notes to return."
     * )
     * @QueryParam(
     *   name="order_by",
     *   nullable=true,
     *   array=true,
     *   description="Order by fields. Must be an array ie. &order_by[name]=ASC&order_by[description]=DESC"
     * )
     * @QueryParam(
     *   name="filters",
     *   nullable=true,
     *   array=true,
     *   description="Filter by fields. Must be an array ie. &filters[id]=3"
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $criteria);

        $result = $this->getProgramHandler()
            ->findProgramDTOsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['programs'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a Program.
     *
     * @ApiDoc(
     *   section = "Program",
     *   description = "Create a Program.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ProgramType",
     *   output="Ilios\CoreBundle\Entity\Program",
     *   statusCodes={
     *     201 = "Created Program.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $handler = $this->getProgramHandler();

            $program = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $program)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getProgramHandler()->updateProgram($program, true, false);

            $answer['programs'] = [$program];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Program.
     *
     * @ApiDoc(
     *   section = "Program",
     *   description = "Update a Program entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ProgramType",
     *   output="Ilios\CoreBundle\Entity\Program",
     *   statusCodes={
     *     200 = "Updated Program.",
     *     201 = "Created Program.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function putAction(Request $request, $id)
    {
        try {
            $program = $this->getProgramHandler()
                ->findProgramBy(['id'=> $id]);
            if ($program) {
                $code = Codes::HTTP_OK;
            } else {
                $program = $this->getProgramHandler()
                    ->createProgram();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getProgramHandler();

            $program = $handler->put(
                $program,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $program)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getProgramHandler()->updateProgram($program, true, true);

            $answer['program'] = $program;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a Program.
     *
     * @ApiDoc(
     *   section = "Program",
     *   description = "Delete a Program entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "Program identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Program.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal ProgramInterface $program
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $program = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $program)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getProgramHandler()
                ->deleteProgram($program);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return ProgramInterface $program
     */
    protected function getOr404($id)
    {
        $program = $this->getProgramHandler()
            ->findProgramBy(['id' => $id]);
        if (!$program) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $program;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('program')) {
            return $request->request->get('program');
        }

        return $request->request->all();
    }

    /**
     * @return ProgramHandler
     */
    protected function getProgramHandler()
    {
        return $this->container->get('ilioscore.program.handler');
    }
}
