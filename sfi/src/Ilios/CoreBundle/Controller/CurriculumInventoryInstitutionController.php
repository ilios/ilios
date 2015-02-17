<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\CurriculumInventoryInstitutionHandler;
use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;

/**
 * CurriculumInventoryInstitution controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("CurriculumInventoryInstitution")
 */
class CurriculumInventoryInstitutionController extends FOSRestController
{
    
    /**
     * Get a CurriculumInventoryInstitution
     *
     * @ApiDoc(
     *   description = "Get a CurriculumInventoryInstitution.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="school",
     *        "dataType"="",
     *        "requirement"="",
     *        "description"="CurriculumInventoryInstitution identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryInstitution",
     *   statusCodes={
     *     200 = "CurriculumInventoryInstitution.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function getAction(Request $request, $id)
    {
        $answer['curriculumInventoryInstitution'] = $this->getOr404($id);

        return $answer;
    }
    /**
     * Get all CurriculumInventoryInstitution.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all CurriculumInventoryInstitution.",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryInstitution",
     *   statusCodes = {
     *     200 = "List of all CurriculumInventoryInstitution",
     *     204 = "No content. Nothing to list."
     *   }
     * )
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
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
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : array();

        $criteria = array_map(function ($item) {
            $item = $item == 'null'?null:$item;
            $item = $item == 'false'?false:$item;
            $item = $item == 'true'?true:$item;
            return $item;
        }, $criteria);

        $result = $this->getCurriculumInventoryInstitutionHandler()
            ->findCurriculumInventoryInstitutionsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );
        //If there are no matches return an empty array
        $answer['curriculumInventoryInstitutions'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a CurriculumInventoryInstitution.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a CurriculumInventoryInstitution.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventoryInstitutionType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryInstitution",
     *   statusCodes={
     *     201 = "Created CurriculumInventoryInstitution.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $new  =  $this->getCurriculumInventoryInstitutionHandler()->post($this->getPostData($request));
            $answer['curriculumInventoryInstitution'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CurriculumInventoryInstitution.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a CurriculumInventoryInstitution entity.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventoryInstitutionType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryInstitution",
     *   statusCodes={
     *     200 = "Updated CurriculumInventoryInstitution.",
     *     201 = "Created CurriculumInventoryInstitution.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $entity
     *
     * @return Response
     */
    public function putAction(Request $request, $id)
    {
        try {
            $curriculumInventoryInstitution = $this->getCurriculumInventoryInstitutionHandler()
                ->findCurriculumInventoryInstitutionBy(['school'=> $id]);
            if ($curriculumInventoryInstitution) {
                $answer['curriculumInventoryInstitution'] =
                    $this->getCurriculumInventoryInstitutionHandler()->put(
                        $curriculumInventoryInstitution,
                        $this->getPostData($request)
                    );
                $code = Codes::HTTP_OK;
            } else {
                $answer['curriculumInventoryInstitution'] =
                    $this->getCurriculumInventoryInstitutionHandler()->post($this->getPostData($request));
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a CurriculumInventoryInstitution.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a CurriculumInventoryInstitution.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventoryInstitutionType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryInstitution",
     *   requirements={
     *     {
     *         "name"="school",
     *         "dataType"="",
     *         "requirement"="",
     *         "description"="CurriculumInventoryInstitution identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated CurriculumInventoryInstitution.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $entity
     *
     * @return Response
     */
    public function patchAction(Request $request, $id)
    {
        $answer['curriculumInventoryInstitution'] =
            $this->getCurriculumInventoryInstitutionHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a CurriculumInventoryInstitution.
     *
     * @ApiDoc(
     *   description = "Delete a CurriculumInventoryInstitution entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "school",
     *         "dataType" = "",
     *         "requirement" = "",
     *         "description" = "CurriculumInventoryInstitution identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted CurriculumInventoryInstitution.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $curriculumInventoryInstitution = $this->getOr404($id);
        try {
            $this->getCurriculumInventoryInstitutionHandler()
                ->deleteCurriculumInventoryInstitution($curriculumInventoryInstitution);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CurriculumInventoryInstitutionInterface $entity
     */
    protected function getOr404($id)
    {
        $entity = $this->getCurriculumInventoryInstitutionHandler()
            ->findCurriculumInventoryInstitutionBy(['school' => $id]);
        if (!$entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }
   /**
    * Parse the request for the form data
    *
    * @param Request $request
    * @return array
     */
    protected function getPostData(Request $request)
    {
        return $request->request->get('curriculumInventoryInstitution', array());
    }
    /**
     * @return CurriculumInventoryInstitutionHandler
     */
    protected function getCurriculumInventoryInstitutionHandler()
    {
        return $this->container->get('ilioscore.curriculuminventoryinstitution.handler');
    }
}
