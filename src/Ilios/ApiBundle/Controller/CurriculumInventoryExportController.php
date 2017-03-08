<?php

namespace Ilios\ApiBundle\Controller;

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
        $class = $manager->getClass() . '[]';

        $json = $this->extractJsonFromRequest($request, $object, 'POST');
        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');

        /** @var UserInterface $currentUser */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        $exporter = $this->container->get('ilioscore.curriculum_inventory.exporter');
        /** @var CurriculumInventoryExportInterface $export */
        foreach ($entities as $export) {
            $export->setCreatedBy($currentUser);
            $this->authorizeEntity($export, 'create');

            // generate and set the report document
            $document = $exporter->getXmlReport($export->getReport());
            $export->setDocument($document->saveXML());

            $this->validateEntity($export);
            $manager->update($export, false);
        }


        $manager->flushAndClear();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }
}
