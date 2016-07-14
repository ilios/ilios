<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryDownloadController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("CurriculumInventoryDownloads")
 */
class CurriculumInventoryDownloadController extends FOSRestController
{
    /**
     * Downloads the curriculum inventory report document for a given report.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryDownload",
     *   description = "Downloads a curriculum inventory report document for a given report id.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="token",
     *        "dataType"="string",
     *        "description"="The curriculum inventory report's download token."
     *     }
     *   },
     *   statusCodes={
     *     200 = "OK",
     *     401 = "Unauthorized.",
     *     403 = "Access denied.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $token
     *
     * @return Response
     */
    public function getAction($token)
    {
        $manager = $this->container->get('ilioscore.curriculuminventoryreport.manager');
        /* @var CurriculumInventoryReportInterface $curriculumInventoryReport */
        $curriculumInventoryReport = $manager->findOneBy(['token' => $token]);

        if (! $curriculumInventoryReport) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $token));
        }

        $document = $this->getExportedDocument($curriculumInventoryReport);

        $response = new Response($document);
        $response->headers->set('Content-Type', 'application/xml; charset="utf-8"');
        $response->headers->set('Content-disposition', 'attachment; filename="report.xml"');
        return $response;
    }

    /**
     * Retrieves the report document for a given curriculum inventory report.
     *
     * @param CurriculumInventoryReportInterface $report
     * @return string
     */
    protected function getExportedDocument(CurriculumInventoryReportInterface $report)
    {
        // check if the report has been exported.
        // if so, pull the document from the database.
        $manager = $this->container->get('ilioscore.curriculuminventoryexport.manager');
        $export = $manager->findOneBy(['report' => $report->getId()]);
        if ($export) {
            return $export->getDocument();
        }

        // otherwise, generate a document on the fly.
        return $this->generateReportDocument($report);
    }

    /**
     * Generates a report document on the fly for a given report.
     *
     * @param CurriculumInventoryReportInterface $report
     * @return string The report document.
     */
    protected function generateReportDocument(CurriculumInventoryReportInterface $report)
    {
        $xml = $this->container->get('ilioscore.curriculum_inventory.exporter')->getXmlReport($report);
        return $xml->saveXML();
    }
}
