<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;

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
            $data = $this->getPostData($request);
            $data['document'] = 'lorem ipsum'; // fake the data document, we'll generate/set the real one further down.

            $handler = $this->container->get('ilioscore.curriculuminventoryexport.handler');
            /** @var CurriculumInventoryExportInterface $curriculumInventoryExport */
            $curriculumInventoryExport = $handler->post($data);

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $curriculumInventoryExport)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $currentUser = $this->get('security.token_storage')->getToken()->getUser();
            $curriculumInventoryExport->setCreatedBy($currentUser);

            // generate and set the report document
            $exporter = $this->container->get('ilioscore.curriculum_inventory.exporter');
            $document = $exporter->getXmlReport($curriculumInventoryExport->getReport());
            $curriculumInventoryExport->setDocument($document->saveXML());

            $manager = $this->container->get('ilioscore.curriculuminventoryexport.manager');
            $manager->update($curriculumInventoryExport, true, false);

            // OF NOTE:
            // We remove the document before returning the export to keep the payload at a reasonable size.
            // The exported report document can be retrieved via the curriculum inventory download controller.
            // [ST 2015/09/21]
            //$curriculumInventoryExport->setDocument('');
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
        if ($request->request->has('curriculumInventoryExport')) {
            return $request->request->get('curriculumInventoryExport');
        }

        return $request->request->all();
    }
}
