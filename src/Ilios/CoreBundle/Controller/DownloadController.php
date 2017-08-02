<?php
namespace Ilios\CoreBundle\Controller;

use Ilios\CoreBundle\Entity\Manager\LearningMaterialManager;
use Ilios\CoreBundle\Service\IliosFileSystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

/**
 * Class DownloadController
 */
class DownloadController extends AbstractController
{

    public function learningMaterialAction(
        $token,
        LearningMaterialManager $learningMaterialManager,
        IliosFileSystem $iliosFileSystem,
        Request $request
    ) {
        $learningMaterial = $learningMaterialManager->findOneBy(['token' => $token]);

        if (!$learningMaterial) {
            throw new NotFoundHttpException();
        }

        $path = $learningMaterial->getRelativePath();
        if (!$path) {
            throw new \Exception(
                "No valid path for learning material with token: " . $token
            );
        }

        $file = $iliosFileSystem->getFile($learningMaterial->getRelativePath());

        if (false === $file) {
            throw new Exception('File not found for learning material #' . $learningMaterial->getId());
        }

        $headers = array(
            'Content-Type' => $learningMaterial->getMimetype(),
            'Content-Disposition' => 'attachment; filename="' . $learningMaterial->getFilename() . '"'
        );

        // d/l PDFs inline if requested so.
        if ('application/pdf' === $headers['Content-Type'] && $request->query->has('inline')) {
            $headers['Content-Disposition'] = 'inline';
        }

        return new Response(file_get_contents($file->getPathname()), 200, $headers);
    }
}
