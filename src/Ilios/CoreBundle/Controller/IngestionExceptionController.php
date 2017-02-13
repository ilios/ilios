<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Entity\IngestionExceptionInterface;

/**
 * Class IngestionExceptionController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("IngestionExceptions")
 *
 */
class IngestionExceptionController
{
    /**
     * Get a IngestionException
     *
     * @ApiDoc(
     *   section = "IngestionException",
     *   description = "Get a IngestionException.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="IngestionException identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\IngestionException",
     *   statusCodes={
     *     200 = "IngestionException.",
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
        $ingestionException = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $ingestionException)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['ingestionExceptions'][] = $ingestionException;

        return $answer;
    }

    /**
     * Get all IngestionException.
     *
     * @ApiDoc(
     *   section = "IngestionException",
     *   description = "Get all IngestionException.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\IngestionException",
     *   statusCodes = {
     *     200 = "List of all IngestionException",
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

        $manager = $this->container->get('ilioscore.ingestionexception.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['ingestionExceptions'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return IngestionExceptionInterface
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.ingestionexception.manager');
        $ingestionException = $manager->findOneBy(['id' => $id]);
        if (!$ingestionException) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $ingestionException;
    }
}
