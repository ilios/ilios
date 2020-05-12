<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\DTO\ObjectiveDTO;
use App\Entity\DTO\ObjectiveV1DTO;
use App\Entity\Manager\CourseObjectiveManager;
use App\Entity\Manager\ObjectiveManager;
use App\Entity\Manager\ProgramYearObjectiveManager;
use App\Entity\Manager\SessionObjectiveManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ObjectiveController
 * @package App\Controller
 */
class ObjectiveController extends ApiController
{
    /**
     * @var CourseObjectiveManager
     */
    protected $courseObjectiveManager;

    /**
     * @var ProgramYearObjectiveManager
     */
    protected $programYearObjectiveManager;

    /**
     * @var SessionObjectiveManager
     */
    protected $sessionObjectiveManager;

    /**
     * @required
     */
    public function setup(
        ObjectiveManager $objectiveManager,
        CourseObjectiveManager $courseObjectiveManager,
        ProgramYearObjectiveManager $programYearObjectiveManager,
        SessionObjectiveManager $sessionObjectiveManager
    ) {
        $this->courseObjectiveManager = $courseObjectiveManager;
        $this->programYearObjectiveManager = $programYearObjectiveManager;
        $this->sessionObjectiveManager = $sessionObjectiveManager;
    }

    public function get($version, $object, $id)
    {
        $manager = $this->getManager($object);
        $dto = null;
        if ('v1' === $version) {
            $dto = $manager->findV1DTOBy(['id' => $id]);
        } else {
            $dto = $manager->findDTOBy(['id' => $id]);

        }

        if (! $dto) {
            $name = ucfirst($this->getSingularResponseKey($object));
            throw new NotFoundHttpException(sprintf("%s with id '%s' was not found.", $name, $id));
        }

        return $this->resultsToResponse([$dto], $this->getPluralResponseKey($object), Response::HTTP_OK);
    }

    public function getAll(
        $version,
        $object,
        Request $request
    ) {
        $parameters = $this->extractParameters($request);
        $manager = $this->getManager($object);
        if ('v1' === $version) {
            $result = $manager->findV1DTOsBy(
                $parameters['criteria'],
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );
        } else {
            $result = $manager->findDTOsBy(
                $parameters['criteria'],
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );
        }
        return $this->resultsToResponse($result, $this->getPluralResponseKey($object), Response::HTTP_OK);
    }
}
