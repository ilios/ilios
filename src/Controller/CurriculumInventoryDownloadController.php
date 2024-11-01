<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CurriculumInventoryExportRepository;
use App\Repository\CurriculumInventoryReportRepository;
use App\Service\CurriculumInventory\Exporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\CurriculumInventoryReportInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CurriculumInventoryDownloadController
 */
class CurriculumInventoryDownloadController extends AbstractController
{
    /**
     * Downloads the curriculum inventory report document for a given report.
     */
    #[Route(
        '/ci-report-dl/{token}',
        requirements: [
            'token' => '^[a-zA-Z0-9]{64}$',
        ],
        methods: ['GET'],
    )]
    public function getAction(
        string $token,
        CurriculumInventoryReportRepository $reportRepository,
        CurriculumInventoryExportRepository $exportManager,
        Exporter $exporter
    ): Response {
        /** @var ?CurriculumInventoryReportInterface $curriculumInventoryReport */
        $curriculumInventoryReport = $reportRepository->findOneBy(['token' => $token]);

        if (! $curriculumInventoryReport) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $token));
        }

        $document = $this->getExportedDocument($curriculumInventoryReport, $exportManager, $exporter);

        $response = new Response($document);
        $response->headers->set('Content-Type', 'application/xml; charset="utf-8"');
        $response->headers->set('Content-disposition', 'attachment; filename="report.xml"');

        // Set a cookie in the response so that the client side can better deal with long-running d/l requests.
        // This cookie must be accessible by JS on the client, so HttpOnly must be explicitly set to FALSE.
        $cookie = new Cookie(
            'report-download-' . $curriculumInventoryReport->getId(),
            null,
            0,
            '/',
            null,
            null,
            false,
            false,
            Cookie::SAMESITE_LAX
        );
        $response->headers->setCookie($cookie);
        return $response;
    }

    /**
     * Retrieves the report document for a given curriculum inventory report.
     */
    protected function getExportedDocument(
        CurriculumInventoryReportInterface $report,
        CurriculumInventoryExportRepository $repository,
        Exporter $exporter
    ): string {
        // check if the report has been exported.
        // if so, pull the document from the database.
        $export = $repository->findOneBy(['report' => $report->getId()]);
        if ($export) {
            return $export->getDocument();
        }

        // otherwise, generate a document on the fly.
        return $exporter->getXmlReport($report);
    }
}
