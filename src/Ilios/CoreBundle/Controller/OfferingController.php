<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\OfferingHandler;
use Ilios\CoreBundle\Entity\OfferingInterface;

/**
 * Class OfferingController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Offerings")
 */
class OfferingController extends FOSRestController
{
    /**
     * Get a Offering
     *
     * @ApiDoc(
     *   section = "Offering",
     *   description = "Get a Offering.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Offering identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   statusCodes={
     *     200 = "Offering.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {
        $offering = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $offering)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['offerings'][] = $offering;

        return $answer;
    }

    /**
     * Get all Offering.
     *
     * @ApiDoc(
     *   section = "Offering",
     *   description = "Get all Offering.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   statusCodes = {
     *     200 = "List of all Offering",
     *     204 = "No content. Nothing to list."
     *   }
     * )
     *
     * @QueryParam(
     *   name="offset",
     *   requirements="\d+",
     *   nullable=true,
     *   description="Offset from which to start listing notes."
     * )
     * @QueryParam(
     *   name="limit",
     *   requirements="\d+",
     *   default="20",
     *   description="How many notes to return."
     * )
     * @QueryParam(
     *   name="order_by",
     *   nullable=true,
     *   array=true,
     *   description="Order by fields. Must be an array ie. &order_by[name]=ASC&order_by[description]=DESC"
     * )
     * @QueryParam(
     *   name="filters",
     *   nullable=true,
     *   array=true,
     *   description="Filter by fields. Must be an array ie. &filters[id]=3"
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $criteria);

        if (array_key_exists('startDate', $criteria)) {
            $criteria['startDate'] = new \DateTime($criteria['startDate']);
        }
        if (array_key_exists('endDate', $criteria)) {
            $criteria['endDate'] = new \DateTime($criteria['endDate']);
        }
        if (array_key_exists('updatedAt', $criteria)) {
            $criteria['updatedAt'] = new \DateTime($criteria['updatedAt']);
        }
        
        $result = $this->getOfferingHandler()
            ->findOfferingsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['offerings'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a Offering.
     *
     * @ApiDoc(
     *   section = "Offering",
     *   description = "Create a Offering.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\OfferingType",
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   statusCodes={
     *     201 = "Created Offering.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $handler = $this->getOfferingHandler();

            $offering = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $offering)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getOfferingHandler()->updateOffering($offering, true, false);

            $session = $offering->getSession();
            if ($session && $session->isPublished()) {
                $this->createAlertForNewOffering($offering);
            }

            $answer['offerings'] = [$offering];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Offering.
     *
     * @ApiDoc(
     *   section = "Offering",
     *   description = "Update a Offering entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\OfferingType",
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   statusCodes={
     *     200 = "Updated Offering.",
     *     201 = "Created Offering.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function putAction(Request $request, $id)
    {
        try {
            $offering = $this->getOfferingHandler()
                ->findOfferingBy(['id'=> $id]);
            if ($offering) {
                $code = Codes::HTTP_OK;
            } else {
                $offering = $this->getOfferingHandler()
                    ->createOffering();
                $code = Codes::HTTP_CREATED;
            }

            // capture the values of offering properties pre-update
            $alertProperties = $offering->getAlertProperties();

            $handler = $this->getOfferingHandler();

            $offering = $handler->put(
                $offering,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $offering)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getOfferingHandler()->updateOffering($offering, true, true);

            $session = $offering->getSession();
            if ($session && $session->isPublished()) {
                if (Codes::HTTP_CREATED === $code) {
                    $this->createAlertForNewOffering($offering);
                } else {
                    $this->createOrUpdateAlertForUpdatedOffering(
                        $offering,
                        $alertProperties
                    );
                }
            }

            $answer['offering'] = $offering;

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a Offering.
     *
     * @ApiDoc(
     *   section = "Offering",
     *   description = "Delete a Offering entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "Offering identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Offering.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal OfferingInterface $offering
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $offering = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $offering)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getOfferingHandler()
                ->deleteOffering($offering);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return OfferingInterface $offering
     */
    protected function getOr404($id)
    {
        $offering = $this->getOfferingHandler()
            ->findOfferingBy(['id' => $id]);
        if (!$offering) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $offering;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('offering')) {
            return $request->request->get('offering');
        }

        return $request->request->all();
    }

    /**
     * @return OfferingHandler
     */
    protected function getOfferingHandler()
    {
        return $this->container->get('ilioscore.offering.handler');
    }

    /**
     * @param OfferingInterface $offering
     */
    protected function createAlertForNewOffering(OfferingInterface $offering)
    {
        // create new alert for this offering
        $alertManager = $this->container->get('ilioscore.alert.manager');
        $alertChangeTypeManager = $this->container->get('ilioscore.alertchangetype.manager');
        $alert = $alertManager->createAlert();
        $alert->addChangeType($alertChangeTypeManager->findAlertChangeTypeBy([
            'id' => AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING]));
        $alert->addInstigator($this->getUser());
        $alert->addRecipient($offering->getSession()->getCourse()->getSchool());
        $alert->setTableName('offering');
        $alert->setTableRowId($offering->getId());
        $alertManager->updateAlert($alert, true, false);
    }

    /**
     * @param OfferingInterface $offering
     * @param array $originalProperties
     */
    protected function createOrUpdateAlertForUpdatedOffering(
        OfferingInterface $offering,
        array $originalProperties
    ) {
        $updatedProperties = $offering->getAlertProperties();

        $changeTypes = [];
        if ($updatedProperties['startDate'] !== $originalProperties['startDate']) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_TIME;
        }
        if ($updatedProperties['endDate'] !== $originalProperties['endDate']) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_TIME;
        }
        if ($updatedProperties['room'] !== $originalProperties['room']) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_LOCATION;
        }
        $instructorIdsDiff = array_merge(
            array_diff($updatedProperties['instructors'], $originalProperties['instructors']),
            array_diff($originalProperties['instructors'], $updatedProperties['instructors'])
        );
        if (! empty($instructorIdsDiff)) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR;
        }
        $instructorGroupIdsDiff = array_merge(
            array_diff($updatedProperties['instructorGroups'], $originalProperties['instructorGroups']),
            array_diff($originalProperties['instructorGroups'], $updatedProperties['instructorGroups'])
        );
        if (! empty($instructorGroupIdsDiff)) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR;
        }
        $learnerIdsDiff = array_merge(
            array_diff($updatedProperties['learners'], $originalProperties['learners']),
            array_diff($originalProperties['learners'], $updatedProperties['learners'])
        );
        if (! empty($learnerIdsDiff)) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP;
        }
        $learnerGroupIdsDiff = array_merge(
            array_diff($updatedProperties['learnerGroups'], $originalProperties['learnerGroups']),
            array_diff($originalProperties['learnerGroups'], $updatedProperties['learnerGroups'])
        );
        if (! empty($learnerGroupIdsDiff)) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP;
        }

        if (empty($changeTypes)) {
            return;
        }
        array_unique($changeTypes);

        $alertManager = $this->container->get('ilioscore.alert.manager');
        $alertChangeTypeManager = $this->container->get('ilioscore.alertchangetype.manager');

        $alert = $alertManager->findAlertBy([
            'dispatched' => false,
            'tableName' => 'offering',
            'tableRowId' => $offering->getId()
        ]);

        if (! $alert) {
            $recipient = $offering->getSchool();
            if (! $recipient) {
                return; // SOL.
            }
            $alert = $alertManager->createAlert();
            $alert->addRecipient($recipient);
            $alert->setTableName('offering');
            $alert->setTableRowId($offering->getId());
            $alert->addInstigator($this->getUser());
        }

        foreach ($changeTypes as $type) {
            $changeType = $alertChangeTypeManager->findAlertChangeTypeBy(['id' => $type]);
            if ($changeType && ! $alert->getChangeTypes()->contains($changeType)) {
                $alert->addChangeType($changeType);
            }
        }

        $alertManager->updateAlert($alert, true, (boolean) $alert->getId());
    }
}
