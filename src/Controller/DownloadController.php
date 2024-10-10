<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\LearningMaterialRepository;
use App\Service\Config;
use App\Service\IliosFileSystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DownloadController
 */
class DownloadController extends AbstractController
{
    #[Route(
        '/lm/{token}',
        requirements: [
            'token' => '^[a-zA-Z0-9]{64}$',
        ],
        methods: ['GET'],
    )]
    public function downloadMaterials(
        string $token,
        LearningMaterialRepository $learningMaterialRepository,
        IliosFileSystem $iliosFileSystem,
        Request $request,
        Config $config
    ): Response {
        if ($config->get('learningMaterialsDisabled') === true) {
            return new Response(
                'Learning Materials are disabled on this instance.',
                200,
            );
        }
        $learningMaterial = $learningMaterialRepository->findOneBy(['token' => $token]);

        if (!$learningMaterial) {
            throw new NotFoundHttpException();
        }

        $path = $learningMaterial->getRelativePath();
        if (!$path) {
            throw new Exception(
                "No valid path for learning material with token: " . $token
            );
        }

        $fileContents = $iliosFileSystem->getFileContents($learningMaterial->getRelativePath());

        if (false === $fileContents) {
            throw new Exception('File not found for learning material #' . $learningMaterial->getId());
        }

        $headers = [
            'Content-Type' => $learningMaterial->getMimetype(),
            'Content-Disposition' => 'attachment; filename="' . $learningMaterial->getFilename() . '"',
        ];

        // d/l PDFs inline if requested so.
        if ('application/pdf' === $headers['Content-Type'] && $request->query->has('inline')) {
            $headers['Content-Disposition'] = 'inline';
        }

        return new Response($fileContents, 200, $headers);
    }
}
