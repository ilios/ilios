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
        $dto = $manager->findDTOBy(['id' => $id]);

        if (! $dto) {
            $name = ucfirst($this->getSingularResponseKey($object));
            throw new NotFoundHttpException(sprintf("%s with id '%s' was not found.", $name, $id));
        }

        if ($version === 'v1') {
            $dto = new ObjectiveV1DTO(
                $dto,
                $this->courseObjectiveManager,
                $this->programYearObjectiveManager,
                $this->sessionObjectiveManager
            );
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
        $result = $manager->findDTOsBy(
            $parameters['criteria'],
            $parameters['orderBy'],
            $parameters['limit'],
            $parameters['offset']
        );
        $courseObjectiveManager = $this->courseObjectiveManager;
        $programYearObjectiveManager = $this->programYearObjectiveManager;
        $sessionObjectiveManager = $this->sessionObjectiveManager;

        if ($version === 'v1') {
            $result = array_map(function (ObjectiveDTO $objectiveDTO) use (
                $courseObjectiveManager,
                $programYearObjectiveManager,
                $sessionObjectiveManager
            ) {
                return new ObjectiveV1DTO(
                    $objectiveDTO,
                    $courseObjectiveManager,
                    $programYearObjectiveManager,
                    $sessionObjectiveManager
                );
            }, $result);
        }

        return $this->resultsToResponse($result, $this->getPluralResponseKey($object), Response::HTTP_OK);
    }
}
