<?php

namespace Ilios\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use \SplFileObject;

/**
 * Class BulkUserCreationController
 * @package Ilios\WebBundle\Controller
 */
class BulkUserCreationController extends Controller
{
    public function uploadAction(Request $request)
    {
        $schoolId = $request->request->get('school');

        if (is_null($schoolId)) {
            return new JsonResponse(array(
                'errors' => 'No school parameter was found in the request'
            ), JsonResponse::HTTP_BAD_REQUEST);
        }

        $schoolManager = $this->container->get('ilioscore.school.manager');
        $school = $schoolManager->findSchoolBy(['id'=>$schoolId]);
        if (is_null($school)) {
            return new JsonResponse(array(
                'errors' => "The school {$schoolId} was not found"
            ), JsonResponse::HTTP_BAD_REQUEST);
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

        $userManager = $this->container->get('ilioscore.user.manager');
        $validator = $this->get('validator');
        $authChecker = $this->get('security.authorization_checker');

        $file = $uploadedFile->openFile();
        $file->setFlags(SplFileObject::READ_CSV);
        $file->setCsvControl("\t");
        
        $results = [];

        foreach ($file as $data) {
            //drop blank lines
            if (count($data) == 1 and is_null($data[0])) {
                continue;
            }
            $result = $data;
            if (count($data) >= 7) {
                list($last, $first, $middel, $phone, $email, $campusId, $otherId) = $data;

                $user = $userManager->createUser();
                $user->setLastName($last);
                $user->setFirstName($first);
                $user->setMiddleName($middel);
                $user->setPhone($phone);
                $user->setEmail($email);
                $user->setCampusId($campusId);
                $user->setOtherId($otherId);
                $user->setSchool($school);

                if (! $authChecker->isGranted('create', $user)) {
                    throw $this->createAccessDeniedException('Unauthorized access!');
                }

                $errors = $validator->validate($user);

                if (count($errors) > 0) {
                    $errorsString = (string) $errors;
                    $result[] = 'error';
                    $result[] = $errorsString;
                } else {
                    $userManager->updateUser($user);
                    $result[] = 'success';
                    $result[] = $user->getId();
                }
            } else {
                $result[] = 'error';
                $result[] = 'not enough fields';
            }

            $results[] = $result;
        }

        return new JsonResponse($results, JsonResponse::HTTP_OK);
    }
}
