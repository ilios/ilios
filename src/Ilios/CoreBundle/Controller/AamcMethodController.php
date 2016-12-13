<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Entity\AamcMethodInterface;

/**
 * Class AamcMethodController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("AamcMethods")
 */
class AamcMethodController extends FOSRestController
{
    /**
     * Get a AamcMethod
     *
     * @ApiDoc(
     *   section = "AamcMethod",
     *   description = "Get a AamcMethod.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="string",
     *        "requirement"="\w+",
     *        "description"="AamcMethod identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   statusCodes={
     *     200 = "AamcMethod.",
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
        $manager = $this->container->get('ilioscore.aamcmethod.manager');

        $aamcMethod = $manager->findDTOBy(['id' => $id]);
        if (!$aamcMethod) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $aamcMethod)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['aamcMethods'][] = $aamcMethod;

        return $answer;
    }

    /**
     * Get all AamcMethod.
     *
     * @ApiDoc(
     *   section = "AamcMethod",
     *   description = "Get all AamcMethod.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   statusCodes = {
     *     200 = "List of all AamcMethod",
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

        $manager = $this->container->get('ilioscore.aamcmethod.manager');
        $result = $manager->findDTOsBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['aamcMethods'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a AamcMethod.
     *
     * @ApiDoc(
     *   section = "AamcMethod",
     *   description = "Create a AamcMethod.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AamcMethodType",
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   statusCodes={
     *     201 = "Created AamcMethod.",
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
            $handler = $this->container->get('ilioscore.aamcmethod.handler');

            $aamcMethod = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $aamcMethod)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.aamcmethod.manager');
            $manager->update($aamcMethod, true, false);

            $answer['aamcMethods'] = [$aamcMethod];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a AamcMethod.
     *
     * @ApiDoc(
     *   section = "AamcMethod",
     *   description = "Update a AamcMethod entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AamcMethodType",
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   statusCodes={
     *     200 = "Updated AamcMethod.",
     *     201 = "Created AamcMethod.",
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
            $manager = $this->container->get('ilioscore.aamcmethod.manager');
            $aamcMethod = $manager->findOneBy(['id'=> $id]);
            if ($aamcMethod) {
                $code = Codes::HTTP_OK;
            } else {
                $aamcMethod = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.aamcmethod.handler');
            $aamcMethod = $handler->put($aamcMethod, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $aamcMethod)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($aamcMethod, true, true);

            $answer['aamcMethod'] = $aamcMethod;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a AamcMethod.
     *
     * @ApiDoc(
     *   section = "AamcMethod",
     *   description = "Delete a AamcMethod entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "string",
     *         "requirement" = "\w+",
     *         "description" = "AamcMethod identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted AamcMethod.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal AamcMethodInterface $aamcMethod
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $aamcMethod = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $aamcMethod)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.aamcmethod.manager');
            $manager->delete($aamcMethod);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return AamcMethodInterface $aamcMethod
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.aamcmethod.manager');
        $aamcMethod = $manager->findOneBy(['id' => $id]);
        if (!$aamcMethod) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $aamcMethod;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('aamcMethod')) {
            return $request->request->get('aamcMethod');
        }

        return $request->request->all();
    }
}
