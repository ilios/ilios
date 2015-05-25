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
use Ilios\CoreBundle\Handler\CurriculumInventoryInstitutionHandler;
use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;

/**
 * Class CurriculumInventoryInstitutionController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("CurriculumInventoryInstitutions")
 */
class CurriculumInventoryInstitutionController extends FOSRestController
{
    /**
     * Get a CurriculumInventoryInstitution
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryInstitution",
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
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {
        $answer['curriculumInventoryInstitution'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all CurriculumInventoryInstitution.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryInstitution",
     *   description = "Get all CurriculumInventoryInstitution.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryInstitution",
     *   statusCodes = {
     *     200 = "List of all CurriculumInventoryInstitution",
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
     *   section = "CurriculumInventoryInstitution",
     *   description = "Create a CurriculumInventoryInstitution.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventoryInstitutionType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryInstitution",
     *   statusCodes={
     *     201 = "Created CurriculumInventoryInstitution.",
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
            $curriculuminventoryinstitution = $this->getCurriculumInventoryInstitutionHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_curriculuminventoryinstitutions',
                    ['school' => $curriculuminventoryinstitution->getSchool()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CurriculumInventoryInstitution.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryInstitution",
     *   description = "Update a CurriculumInventoryInstitution entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventoryInstitutionType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryInstitution",
     *   statusCodes={
     *     200 = "Updated CurriculumInventoryInstitution.",
     *     201 = "Created CurriculumInventoryInstitution.",
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
            $curriculumInventoryInstitution = $this->getCurriculumInventoryInstitutionHandler()
                ->findCurriculumInventoryInstitutionBy(['school'=> $id]);
            if ($curriculumInventoryInstitution) {
                $code = Codes::HTTP_OK;
            } else {
                $curriculumInventoryInstitution = $this->getCurriculumInventoryInstitutionHandler()->createCurriculumInventoryInstitution();
                $code = Codes::HTTP_CREATED;
            }

            $answer['curriculumInventoryInstitution'] =
                $this->getCurriculumInventoryInstitutionHandler()->put(
                    $curriculumInventoryInstitution,
                    $this->getPostData($request)
                );
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
     *   section = "CurriculumInventoryInstitution",
     *   description = "Partial Update to a CurriculumInventoryInstitution.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventoryInstitutionType",
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
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
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
     *   section = "CurriculumInventoryInstitution",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $curriculumInventoryInstitution = $this->getOr404($id);

        try {
            $this->getCurriculumInventoryInstitutionHandler()->deleteCurriculumInventoryInstitution($curriculumInventoryInstitution);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
     */
    protected function getOr404($id)
    {
        $curriculumInventoryInstitution = $this->getCurriculumInventoryInstitutionHandler()
            ->findCurriculumInventoryInstitutionBy(['school' => $id]);
        if (!$curriculumInventoryInstitution) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $curriculumInventoryInstitution;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('curriculumInventoryInstitution');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return CurriculumInventoryInstitutionHandler
     */
    protected function getCurriculumInventoryInstitutionHandler()
    {
        return $this->container->get('ilioscore.curriculuminventoryinstitution.handler');
    }
}
