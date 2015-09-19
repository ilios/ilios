<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Ilios\CoreBundle\Classes\CurriculumInventory\Exporter;
use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\CurriculumInventoryExportHandler;

/**
 * Class CurriculumInventoryExportController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("CurriculumInventoryExports")
 */
class CurriculumInventoryExportController extends FOSRestController
{
    /**
     * Creates a Curriculum Inventory Export.
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

            /** @var CurriculumInventoryExportInterface $curriculumInventoryExport */
            $curriculumInventoryExport = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $curriculumInventoryExport)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            /** @var Exporter $exporter */
            $exporter = $this->container->get('ilioscore.curriculum_inventory.exporter');

            $document = $exporter->getXmlReport($curriculumInventoryExport->getReport());
            $currentUser = $this->get('security.token_storage')->getToken()->getUser();
            $curriculumInventoryExport->setDocument($document);
            $curriculumInventoryExport->setCreatedBy($currentUser);

            $handler->updateCurriculumInventoryExport(
                $curriculumInventoryExport,
                true,
                false
            );

            // Remove the document before returning the export to keep the payload at a reasonable size.
            // The exported report document can be retrieved via the curriculum inventory download controller.
            $curriculumInventoryExport->setDocument('');
            $answer['curriculumInventoryExports'] = [$curriculumInventoryExport];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
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
