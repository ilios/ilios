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
use Ilios\CoreBundle\Handler\CurriculumInventoryExportHandler;
use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;

/**
 * Class CurriculumInventoryExportController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("CurriculumInventoryExports")
 */
class CurriculumInventoryExportController extends FOSRestController
{
    /**
     * Get a CurriculumInventoryExport
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryExport",
     *   description = "Get a CurriculumInventoryExport.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="CurriculumInventoryExport identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryExport",
     *   statusCodes={
     *     200 = "CurriculumInventoryExport.",
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
        $curriculumInventoryExport = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $curriculumInventoryExport)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['curriculumInventoryExports'][] = $curriculumInventoryExport;

        return $answer;
    }

    /**
     * Get all CurriculumInventoryExport.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryExport",
     *   description = "Get all CurriculumInventoryExport.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryExport",
     *   statusCodes = {
     *     200 = "List of all CurriculumInventoryExport",
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

        $result = $this->getCurriculumInventoryExportHandler()
            ->findCurriculumInventoryExportsBy(
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
        $answer['curriculumInventoryExports'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a CurriculumInventoryExport.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryExport",
     *   description = "Create a CurriculumInventoryExport.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventoryExportType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryExport",
     *   statusCodes={
     *     201 = "Created CurriculumInventoryExport.",
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
            $handler = $this->getCurriculumInventoryExportHandler();

            $curriculumInventoryExport = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $curriculumInventoryExport)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getCurriculumInventoryExportHandler()->updateCurriculumInventoryExport($curriculumInventoryExport, true, false);

            $answer['curriculumInventoryExports'] = [$curriculumInventoryExport];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CurriculumInventoryExportInterface $curriculumInventoryExport
     */
    protected function getOr404($id)
    {
        $curriculumInventoryExport = $this->getCurriculumInventoryExportHandler()
            ->findCurriculumInventoryExportBy(['id' => $id]);
        if (!$curriculumInventoryExport) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $curriculumInventoryExport;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('curriculumInventoryExport');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return CurriculumInventoryExportHandler
     */
    protected function getCurriculumInventoryExportHandler()
    {
        return $this->container->get('ilioscore.curriculuminventoryexport.handler');
    }
}
