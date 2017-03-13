<?php

namespace Ilios\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryDownloadController
 * @package Ilios\CoreBundle\Controller
 */
class CurriculumInventoryDownloadController extends Controller
{
    /**
     * Downloads the curriculum inventory report document for a given report.
     *
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

        // Set a cookie in the response so that the client side can better deal with long-running d/l requests.
        // This cookie must be accessible by JS on the client, so HttpOnly must be explicitly set to FALSE.
        $cookie = new Cookie(
            'report-download-' . $curriculumInventoryReport->getId(),
            true,
            0,
            '/',
            null,
            false,
            false
        );
        $response->headers->setCookie($cookie);
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
