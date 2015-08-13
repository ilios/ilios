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
use Ilios\CoreBundle\Handler\DisciplineHandler;
use Ilios\CoreBundle\Entity\DisciplineInterface;

/**
 * Class DisciplineController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Disciplines")
 */
class DisciplineController extends FOSRestController
{
    /**
     * Get a Discipline
     *
     * @ApiDoc(
     *   section = "Discipline",
     *   description = "Get a Discipline.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Discipline identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Discipline",
     *   statusCodes={
     *     200 = "Discipline.",
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
        $discipline = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $discipline)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['disciplines'][] = $discipline;

        return $answer;
    }

    /**
     * Get all Discipline.
     *
     * @ApiDoc(
     *   section = "Discipline",
     *   description = "Get all Discipline.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Discipline",
     *   statusCodes = {
     *     200 = "List of all Discipline",
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

        $result = $this->getDisciplineHandler()
            ->findDisciplinesBy(
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
        $answer['disciplines'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a Discipline.
     *
     * @ApiDoc(
     *   section = "Discipline",
     *   description = "Create a Discipline.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\DisciplineType",
     *   output="Ilios\CoreBundle\Entity\Discipline",
     *   statusCodes={
     *     201 = "Created Discipline.",
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
            $handler = $this->getDisciplineHandler();

            $discipline = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $discipline)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getDisciplineHandler()->updateDiscipline($discipline, true, false);

            $answer['disciplines'] = [$discipline];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Discipline.
     *
     * @ApiDoc(
     *   section = "Discipline",
     *   description = "Update a Discipline entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\DisciplineType",
     *   output="Ilios\CoreBundle\Entity\Discipline",
     *   statusCodes={
     *     200 = "Updated Discipline.",
     *     201 = "Created Discipline.",
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
            $discipline = $this->getDisciplineHandler()
                ->findDisciplineBy(['id'=> $id]);
            if ($discipline) {
                $code = Codes::HTTP_OK;
            } else {
                $discipline = $this->getDisciplineHandler()
                    ->createDiscipline();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getDisciplineHandler();

            $discipline = $handler->put(
                $discipline,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $discipline)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getDisciplineHandler()->updateDiscipline($discipline, true, true);

            $answer['discipline'] = $discipline;

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a Discipline.
     *
     * @ApiDoc(
     *   section = "Discipline",
     *   description = "Delete a Discipline entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "Discipline identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Discipline.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal DisciplineInterface $discipline
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $discipline = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $discipline)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getDisciplineHandler()
                ->deleteDiscipline($discipline);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return DisciplineInterface $discipline
     */
    protected function getOr404($id)
    {
        $discipline = $this->getDisciplineHandler()
            ->findDisciplineBy(['id' => $id]);
        if (!$discipline) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $discipline;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('discipline');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return DisciplineHandler
     */
    protected function getDisciplineHandler()
    {
        return $this->container->get('ilioscore.discipline.handler');
    }
}
