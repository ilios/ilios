<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\SessionUserInterface;
use App\Entity\CurriculumInventoryExportInterface;
use App\Entity\Manager\CurriculumInventoryExportManager;
use App\Entity\Manager\UserManager;
use App\Entity\UserInterface;
use App\RelationshipVoter\AbstractVoter;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Service\CurriculumInventory\Exporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/{version<v1|v3>}/curriculuminventoryexports")
 */
class CurriculumInventoryExports
{
    /**
     * Create the XML document for a curriculum inventory report
     * @Route("", methods={"POST"})
     */
    public function post(
        string $version,
        Request $request,
        CurriculumInventoryExportManager $manager,
        ApiRequestParser $requestParser,
        TokenStorageInterface $tokenStorage,
        UserManager $userManager,
        AuthorizationCheckerInterface $authorizationChecker,
        ValidatorInterface $validator,
        Exporter $exporter,
        ApiResponseBuilder $builder
    ): Response {
        $class = $manager->getClass() . '[]';
        $entities = $requestParser->extractEntitiesFromPostRequest($request, $class, 'curriculuminventoryexports');

        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $tokenStorage->getToken()->getUser();
        /** @var UserInterface $user */
        $user = $userManager->findOneBy(['id' => $sessionUser->getId()]);
        /** @var CurriculumInventoryExportInterface $export */
        foreach ($entities as $export) {
            $export->setCreatedBy($user);
            if (! $authorizationChecker->isGranted(AbstractVoter::CREATE, $export)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            // generate and set the report document
            $document = $exporter->getXmlReport($export->getReport());
            $export->setDocument($document);

            $errors = $validator->validate($export);
            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
            }
            $manager->update($export, false);
        }

        $manager->flush();

        $ids = array_map(function ($entity) {
            return $entity->getId();
        }, $entities);

        $dtos = $manager->findDTOsBy(['id' => $ids]);

        return $builder->buildResponseForPostRequest(
            'curriculuminventoryexports',
            $dtos,
            Response::HTTP_CREATED,
            $request
        );
    }
}
