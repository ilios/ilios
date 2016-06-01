<?php
namespace Ilios\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class UploadController
 * @package Ilios\CoreBundle\Controller
 */
class UploadController extends Controller
{

    public function uploadAction(Request $request)
    {
        $fs = $this->container->get('ilioscore.temporary_filesystem');

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('create', $fs)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $uploadedFile = $request->files->get('file');
        if (is_null($uploadedFile)) {
            return new JsonResponse(array(
                'errors' => 'No file parameter was found in the request'
            ), JsonResponse::HTTP_BAD_REQUEST);
        }
        if (!$uploadedFile->isValid()) {
            return new JsonResponse(array('errors' => 'File failed to upload'), JsonResponse::HTTP_BAD_REQUEST);
        }
        $hash = $fs->storeFile($uploadedFile);
        $response = array(
            'filename' => $uploadedFile->getClientOriginalName(),
            'fileHash' => $hash
        );
        return new JsonResponse($response, JsonResponse::HTTP_OK);
    }
}
