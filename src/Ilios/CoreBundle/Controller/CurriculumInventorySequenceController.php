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
use Ilios\CoreBundle\Handler\CurriculumInventorySequenceHandler;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;

/**
 * Class CurriculumInventorySequenceController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("CurriculumInventorySequences")
 */
class CurriculumInventorySequenceController extends FOSRestController
{
    /**
     * Get a CurriculumInventorySequence
     *
     * @ApiDoc(
     *   section = "CurriculumInventorySequence",
     *   description = "Get a CurriculumInventorySequence.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="CurriculumInventorySequence identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequence",
     *   statusCodes={
     *     200 = "CurriculumInventorySequence.",
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
        $curriculumInventorySequence = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $curriculumInventorySequence)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['curriculumInventorySequences'][] = $curriculumInventorySequence;

        return $answer;
    }

    /**
     * Get all CurriculumInventorySequence.
     *
     * @ApiDoc(
     *   section = "CurriculumInventorySequence",
     *   description = "Get all CurriculumInventorySequence.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequence",
     *   statusCodes = {
     *     200 = "List of all CurriculumInventorySequence",
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

        $result = $this->getCurriculumInventorySequenceHandler()
            ->findCurriculumInventorySequencesBy(
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
        $answer['curriculumInventorySequences'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a CurriculumInventorySequence.
     *
     * @ApiDoc(
     *   section = "CurriculumInventorySequence",
     *   description = "Create a CurriculumInventorySequence.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventorySequenceType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequence",
     *   statusCodes={
     *     201 = "Created CurriculumInventorySequence.",
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
            $handler = $this->getCurriculumInventorySequenceHandler();

            $curriculumInventorySequence = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $curriculumInventorySequence)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getCurriculumInventorySequenceHandler()->updateCurriculumInventorySequence(
                $curriculumInventorySequence,
                true,
                false
            );

            $answer['curriculumInventorySequences'] = [$curriculumInventorySequence];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CurriculumInventorySequence.
     *
     * @ApiDoc(
     *   section = "CurriculumInventorySequence",
     *   description = "Update a CurriculumInventorySequence entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventorySequenceType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequence",
     *   statusCodes={
     *     200 = "Updated CurriculumInventorySequence.",
     *     201 = "Created CurriculumInventorySequence.",
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
            $curriculumInventorySequence = $this->getCurriculumInventorySequenceHandler()
                ->findCurriculumInventorySequenceBy(['id'=> $id]);
            if ($curriculumInventorySequence) {
                $code = Codes::HTTP_OK;
            } else {
                $curriculumInventorySequence = $this->getCurriculumInventorySequenceHandler()
                    ->createCurriculumInventorySequence();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getCurriculumInventorySequenceHandler();

            $curriculumInventorySequence = $handler->put(
                $curriculumInventorySequence,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $curriculumInventorySequence)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getCurriculumInventorySequenceHandler()->updateCurriculumInventorySequence(
                $curriculumInventorySequence,
                true,
                true
            );

            $answer['curriculumInventorySequence'] = $curriculumInventorySequence;

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a CurriculumInventorySequence.
     *
     * @ApiDoc(
     *   section = "CurriculumInventorySequence",
     *   description = "Delete a CurriculumInventorySequence entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "CurriculumInventorySequence identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted CurriculumInventorySequence.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal CurriculumInventorySequenceInterface $curriculumInventorySequence
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $curriculumInventorySequence = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $curriculumInventorySequence)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getCurriculumInventorySequenceHandler()
                ->deleteCurriculumInventorySequence($curriculumInventorySequence);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CurriculumInventorySequenceInterface $curriculumInventorySequence
     */
    protected function getOr404($id)
    {
        $curriculumInventorySequence = $this->getCurriculumInventorySequenceHandler()
            ->findCurriculumInventorySequenceBy(['id' => $id]);
        if (!$curriculumInventorySequence) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $curriculumInventorySequence;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('curriculumInventorySequence')) {
            return $request->request->get('curriculumInventorySequence');
        }

        return $request->request->all();
    }

    /**
     * @return CurriculumInventorySequenceHandler
     */
    protected function getCurriculumInventorySequenceHandler()
    {
        return $this->container->get('ilioscore.curriculuminventorysequence.handler');
    }
}
