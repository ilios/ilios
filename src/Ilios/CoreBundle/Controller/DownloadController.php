<?php
namespace Ilios\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Exception;

/**
 * Class DownloadController
 * @package Ilios\CoreBundle\Controller
 */
class DownloadController extends Controller
{

    public function learningMaterialAction($token)
    {
        $learningMaterial = $this->container->get('ilioscore.learningmaterial.manager')
            ->findOneBy(['token' => $token]);
        
        if (!$learningMaterial) {
            throw new NotFoundHttpException();
        }

        $path = $learningMaterial->getRelativePath();
        if (!$path) {
            throw new \Exception(
                "No valid path for learning material with token: " . $token
            );
        }
        
        $file = $this->container->get('ilioscore.filesystem')
            ->getFile($learningMaterial->getRelativePath());
        
        if (false === $file) {
            throw new \Exception('File not found for learning material #' . $learningMaterial->getId());
        }
        
        $headers = array(
            'Content-Type' => $learningMaterial->getMimetype(),
            'Content-Disposition' => 'attachment; filename="' . $learningMaterial->getFilename() . '"'
        );


        return new Response(file_get_contents($file->getPathname()), 200, $headers);
    }
}
