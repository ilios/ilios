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
use Ilios\CoreBundle\Handler\TopicHandler;
use Ilios\CoreBundle\Entity\TopicInterface;

/**
 * Class TopicController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Topics")
 */
class TopicController extends FOSRestController
{
    /**
     * Get a Topic
     *
     * @ApiDoc(
     *   section = "Topic",
     *   description = "Get a Topic.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Topic identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Topic",
     *   statusCodes={
     *     200 = "Topic.",
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
        $topic = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $topic)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['topics'][] = $topic;

        return $answer;
    }

    /**
     * Get all Topic.
     *
     * @ApiDoc(
     *   section = "Topic",
     *   description = "Get all Topic.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Topic",
     *   statusCodes = {
     *     200 = "List of all Topic",
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

        $result = $this->getTopicHandler()
            ->findTopicsBy(
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
        $answer['topics'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a Topic.
     *
     * @ApiDoc(
     *   section = "Topic",
     *   description = "Create a Topic.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\TopicType",
     *   output="Ilios\CoreBundle\Entity\Topic",
     *   statusCodes={
     *     201 = "Created Topic.",
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
            $handler = $this->getTopicHandler();

            $topic = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $topic)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getTopicHandler()->updateTopic($topic, true, false);

            $answer['topics'] = [$topic];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Topic.
     *
     * @ApiDoc(
     *   section = "Topic",
     *   description = "Update a Topic entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\TopicType",
     *   output="Ilios\CoreBundle\Entity\Topic",
     *   statusCodes={
     *     200 = "Updated Topic.",
     *     201 = "Created Topic.",
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
            $topic = $this->getTopicHandler()
                ->findTopicBy(['id'=> $id]);
            if ($topic) {
                $code = Codes::HTTP_OK;
            } else {
                $topic = $this->getTopicHandler()
                    ->createTopic();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getTopicHandler();

            $topic = $handler->put(
                $topic,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $topic)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getTopicHandler()->updateTopic($topic, true, true);

            $answer['topic'] = $topic;

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a Topic.
     *
     * @ApiDoc(
     *   section = "Topic",
     *   description = "Delete a Topic entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "Topic identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Topic.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal TopicInterface $topic
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $topic = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $topic)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getTopicHandler()
                ->deleteTopic($topic);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return TopicInterface $topic
     */
    protected function getOr404($id)
    {
        $topic = $this->getTopicHandler()
            ->findTopicBy(['id' => $id]);
        if (!$topic) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $topic;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('topic')) {
            return $request->request->get('topic');
        }

        return $request->request->all();
    }

    /**
     * @return TopicHandler
     */
    protected function getTopicHandler()
    {
        return $this->container->get('ilioscore.topic.handler');
    }
}
