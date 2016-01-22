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
use Ilios\CoreBundle\Handler\CurriculumInventorySequenceBlockHandler;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;

/**
 * Class CurriculumInventorySequenceBlockController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("CurriculumInventorySequenceBlocks")
 */
class CurriculumInventorySequenceBlockController extends FOSRestController
{
    /**
     * Get a CurriculumInventorySequenceBlock
     *
     * @ApiDoc(
     *   section = "CurriculumInventorySequenceBlock",
     *   description = "Get a CurriculumInventorySequenceBlock.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="CurriculumInventorySequenceBlock identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock",
     *   statusCodes={
     *     200 = "CurriculumInventorySequenceBlock.",
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
        $curriculumInventorySequenceBlock = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $curriculumInventorySequenceBlock)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['curriculumInventorySequenceBlocks'][] = $curriculumInventorySequenceBlock;

        return $answer;
    }

    /**
     * Get all CurriculumInventorySequenceBlock.
     *
     * @ApiDoc(
     *   section = "CurriculumInventorySequenceBlock",
     *   description = "Get all CurriculumInventorySequenceBlock.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock",
     *   statusCodes = {
     *     200 = "List of all CurriculumInventorySequenceBlock",
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

        $result = $this->getCurriculumInventorySequenceBlockHandler()
            ->findCurriculumInventorySequenceBlocksBy(
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
        $answer['curriculumInventorySequenceBlocks'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a CurriculumInventorySequenceBlock.
     *
     * @ApiDoc(
     *   section = "CurriculumInventorySequenceBlock",
     *   description = "Create a CurriculumInventorySequenceBlock.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventorySequenceBlockType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock",
     *   statusCodes={
     *     201 = "Created CurriculumInventorySequenceBlock.",
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
            $handler = $this->getCurriculumInventorySequenceBlockHandler();

            $curriculumInventorySequenceBlock = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $curriculumInventorySequenceBlock)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getCurriculumInventorySequenceBlockHandler()->updateCurriculumInventorySequenceBlock(
                $curriculumInventorySequenceBlock,
                true,
                false
            );

            $answer['curriculumInventorySequenceBlocks'] = [$curriculumInventorySequenceBlock];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CurriculumInventorySequenceBlock.
     *
     * @ApiDoc(
     *   section = "CurriculumInventorySequenceBlock",
     *   description = "Update a CurriculumInventorySequenceBlock entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventorySequenceBlockType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock",
     *   statusCodes={
     *     200 = "Updated CurriculumInventorySequenceBlock.",
     *     201 = "Created CurriculumInventorySequenceBlock.",
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
            $curriculumInventorySequenceBlock = $this->getCurriculumInventorySequenceBlockHandler()
                ->findCurriculumInventorySequenceBlockBy(['id'=> $id]);
            if ($curriculumInventorySequenceBlock) {
                $code = Codes::HTTP_OK;
            } else {
                $curriculumInventorySequenceBlock = $this->getCurriculumInventorySequenceBlockHandler()
                    ->createCurriculumInventorySequenceBlock();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getCurriculumInventorySequenceBlockHandler();

            $curriculumInventorySequenceBlock = $handler->put(
                $curriculumInventorySequenceBlock,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $curriculumInventorySequenceBlock)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getCurriculumInventorySequenceBlockHandler()->updateCurriculumInventorySequenceBlock(
                $curriculumInventorySequenceBlock,
                true,
                true
            );

            $answer['curriculumInventorySequenceBlock'] = $curriculumInventorySequenceBlock;

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a CurriculumInventorySequenceBlock.
     *
     * @ApiDoc(
     *   section = "CurriculumInventorySequenceBlock",
     *   description = "Delete a CurriculumInventorySequenceBlock entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "CurriculumInventorySequenceBlock identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted CurriculumInventorySequenceBlock.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $curriculumInventorySequenceBlock = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $curriculumInventorySequenceBlock)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getCurriculumInventorySequenceBlockHandler()
                ->deleteCurriculumInventorySequenceBlock($curriculumInventorySequenceBlock);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     */
    protected function getOr404($id)
    {
        $curriculumInventorySequenceBlock = $this->getCurriculumInventorySequenceBlockHandler()
            ->findCurriculumInventorySequenceBlockBy(['id' => $id]);
        if (!$curriculumInventorySequenceBlock) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $curriculumInventorySequenceBlock;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('curriculumInventorySequenceBlock')) {
            return $request->request->get('curriculumInventorySequenceBlock');
        }

        return $request->request->all();
    }

    /**
     * @return CurriculumInventorySequenceBlockHandler
     */
    protected function getCurriculumInventorySequenceBlockHandler()
    {
        return $this->container->get('ilioscore.curriculuminventorysequenceblock.handler');
    }
}
