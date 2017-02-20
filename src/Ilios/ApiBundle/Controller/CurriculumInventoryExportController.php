<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CurriculumInventoryExport
 * CurriculumInventoryExports can only be POSTed nothing else
 * @package Ilios\ApiBundle\Controller
 */
class CurriculumInventoryExportController extends NonDtoApiController
{
    public function fourOhFourAction()
    {
        throw new NotFoundHttpException('Curriculum Inventory Exports can only be created');
    }

    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);
        $class = $manager->getClass() . '[]';

        $json = $this->extractDataFromRequest($request, $object);
        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');

        /** @var UserInterface $currentUser */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        $exporter = $this->container->get('ilioscore.curriculum_inventory.exporter');
        $authChecker = $this->get('security.authorization_checker');
        $validator = $this->container->get('validator');
        /** @var CurriculumInventoryExportInterface $export */
        foreach ($entities as $export) {
            $export->setCreatedBy($currentUser);
            if (! $authChecker->isGranted('create', $export)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }
            // generate and set the report document
            $document = $exporter->getXmlReport($export->getReport());
            $export->setDocument($document->saveXML());

            $errors = $validator->validate($export);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
            }
            $manager->update($export, false);
        }


        $manager->flushAndClear();

        return $this->createResponse($object, $entities, Response::HTTP_CREATED);
    }
}
