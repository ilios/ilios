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
use Ilios\CoreBundle\Handler\TermHandler;
use Ilios\CoreBundle\Entity\TermInterface;

/**
 * Class TermController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Terms")
 */
class TermController extends FOSRestController
{
    /**
     * Get a Term.
     *
     * @ApiDoc(
     *   section = "Term",
     *   description = "Get a Term.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Term identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Term",
     *   statusCodes={
     *     200 = "Term.",
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
        $term = $this->getTermHandler()->findTermDTOBy(['id' => $id]);

        if (! $term) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $term)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['terms'][] = $term;

        return $answer;
    }

    /**
     * Get all Terms.
     *
     * @ApiDoc(
     *   section = "Term",
     *   description = "Get all Terms.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Term",
     *   statusCodes = {
     *     200 = "List of all Terms",
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

        $result = $this->getTermHandler()
            ->findTermDTOsBy(
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
        $answer['terms'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a Term.
     *
     * @ApiDoc(
     *   section = "Term",
     *   description = "Create a Term.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\TermType",
     *   output="Ilios\CoreBundle\Entity\Term",
     *   statusCodes={
     *     201 = "Created Term.",
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
            $handler = $this->getTermHandler();

            $term = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $term)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getTermHandler()->updateTerm($term, true, false);

            $answer['terms'] = [$term];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Term.
     *
     * @ApiDoc(
     *   section = "Term",
     *   description = "Update a Term entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\TermType",
     *   output="Ilios\CoreBundle\Entity\Term",
     *   statusCodes={
     *     200 = "Updated Term.",
     *     201 = "Created Term.",
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
            $term = $this->getTermHandler()
                ->findTermBy(['id'=> $id]);
            if ($term) {
                $code = Codes::HTTP_OK;
            } else {
                $term = $this->getTermHandler()
                    ->createTerm();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getTermHandler();

            $term = $handler->put(
                $term,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $term)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getTermHandler()->updateTerm($term, true, true);

            $answer['term'] = $term;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a Term.
     *
     * @ApiDoc(
     *   section = "Term",
     *   description = "Delete a Term entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "Term identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Term.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal TermInterface $term
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $term = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $term)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getTermHandler()
                ->deleteTerm($term);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get an entity or throw a exception.
     *
     * @param $id
     * @return TermInterface $term
     */
    protected function getOr404($id)
    {
        $term = $this->getTermHandler()
            ->findTermBy(['id' => $id]);
        if (!$term) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $term;
    }

    /**
     * Parse the request for the form data.
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('term')) {
            return $request->request->get('term');
        }

        return $request->request->all();
    }

    /**
     * @return TermHandler
     */
    protected function getTermHandler()
    {
        return $this->container->get('ilioscore.term.handler');
    }
}
