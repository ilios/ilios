<?php

declare(strict_types=1);

namespace App\Controller;

use App\Classes\VoterPermissions;
use App\Service\IliosFileSystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class UploadController
 */
class UploadController extends AbstractController
{
    #[Route('/upload')]
    public function upload(
        Request $request,
        IliosFileSystem $iliosFileSystem,
        AuthorizationCheckerInterface $authorizationChecker
    ): JsonResponse {
        if (! $authorizationChecker->isGranted(VoterPermissions::CREATE_TEMPORARY_FILE, $iliosFileSystem)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $uploadedFile = $request->files->get('file');
        if (is_null($uploadedFile)) {
            return new JsonResponse([
                'errors' => 'Unable to find file in the request. ' .
                            'The uploaded file may have exceeded the maximum allowed size',
            ], Response::HTTP_BAD_REQUEST);
        }
        if (!$uploadedFile->isValid()) {
            return new JsonResponse(['errors' => 'File failed to upload'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $hash = $iliosFileSystem->storeUploadedTemporaryFile($uploadedFile);
        $response = [
            'filename' => $uploadedFile->getClientOriginalName(),
            'fileHash' => $hash,
        ];
        return new JsonResponse($response, JsonResponse::HTTP_OK);
    }
}
