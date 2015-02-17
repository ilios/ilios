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
use Ilios\CoreBundle\Handler\ProgramHandler;
use Ilios\CoreBundle\Entity\ProgramInterface;

/**
 * Program controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Program")
 */
class ProgramController extends FOSRestController
{
    
    /**
     * Get a Program
     *
     * @ApiDoc(
     *   description = "Get a Program.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="",
     *        "description"="Program identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Program",
     *   statusCodes={
     *     200 = "Program.",
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
        $answer['program'] = $this->getOr404($id);

        return $answer;
    }
    /**
     * Get all Program.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all Program.",
     *   output="Ilios\CoreBundle\Entity\Program",
     *   statusCodes = {
     *     200 = "List of all Program",
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

        $result = $this->getProgramHandler()
            ->findProgramsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );
        //If there are no matches return an empty array
        $answer['programs'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a Program.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a Program.",
     *   input="Ilios\CoreBundle\Form\ProgramType",
     *   output="Ilios\CoreBundle\Entity\Program",
     *   statusCodes={
     *     201 = "Created Program.",
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
            $new  =  $this->getProgramHandler()->post($this->getPostData($request));
            $answer['program'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Program.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a Program entity.",
     *   input="Ilios\CoreBundle\Form\ProgramType",
     *   output="Ilios\CoreBundle\Entity\Program",
     *   statusCodes={
     *     200 = "Updated Program.",
     *     201 = "Created Program.",
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
            $program = $this->getProgramHandler()
                ->findProgramBy(['id'=> $id]);
            if ($program) {
                $answer['program'] =
                    $this->getProgramHandler()->put(
                        $program,
                        $this->getPostData($request)
                    );
                $code = Codes::HTTP_OK;
            } else {
                $answer['program'] =
                    $this->getProgramHandler()->post($this->getPostData($request));
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Program.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a Program.",
     *   input="Ilios\CoreBundle\Form\ProgramType",
     *   output="Ilios\CoreBundle\Entity\Program",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="",
     *         "description"="Program identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated Program.",
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
        $answer['program'] =
            $this->getProgramHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a Program.
     *
     * @ApiDoc(
     *   description = "Delete a Program entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "Program identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Program.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal ProgramInterface $program
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $program = $this->getOr404($id);
        try {
            $this->getProgramHandler()
                ->deleteProgram($program);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return ProgramInterface $entity
     */
    protected function getOr404($id)
    {
        $entity = $this->getProgramHandler()
            ->findProgramBy(['id' => $id]);
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
        return $request->request->get('program', array());
    }
    /**
     * @return ProgramHandler
     */
    protected function getProgramHandler()
    {
        return $this->container->get('ilioscore.program.handler');
    }
}
