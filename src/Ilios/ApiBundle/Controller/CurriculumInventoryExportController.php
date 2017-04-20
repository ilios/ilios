<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CurriculumInventoryExport
 * CurriculumInventoryExports can only be POSTed nothing else
 * @package Ilios\ApiBundle\Controller
 */
class CurriculumInventoryExportController extends NonDtoApiController
{
    /**
     * Return a 404 response
     */
    public function fourOhFourAction()
    {
        throw new NotFoundHttpException('Curriculum Inventory Exports can only be created');
    }

    /**
     * Create the XML document for a curriculum inventory report
     *
     * @inheritdoc
     */
    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);
        $userManager = $this->container->get('ilioscore.user.manager');
        $class = $manager->getClass() . '[]';

        $json = $this->extractJsonFromRequest($request, $object, 'POST');
        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');

        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $this->get('security.token_storage')->getToken()->getUser();
        $user = $userManager->findOneBy(['id' => $sessionUser->getId()]);
        $exporter = $this->container->get('ilioscore.curriculum_inventory.exporter');
        /** @var CurriculumInventoryExportInterface $export */
        foreach ($entities as $export) {
            $export->setCreatedBy($user);
            $this->authorizeEntity($export, 'create');

            // generate and set the report document
            $document = $exporter->getXmlReport($export->getReport());
            $export->setDocument($document->saveXML());

            $this->validateEntity($export);
            $manager->update($export, false);
        }


        $manager->flush();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }
}
