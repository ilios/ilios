<?php

namespace Tests\IliosApiBundle\Endpoints;

use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;
use Tests\CoreBundle\DataLoader\InstructorGroupData;
use Tests\CoreBundle\DataLoader\LearnerGroupData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * Offering API endpoint Test.
 * @group api_1
 */
class OfferingTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'offerings';
    protected $skipDates = false;

    /**
     * Reset date skipping for each test
     */
    public function setUp()
    {
        parent::setUp();
        $this->skipDates = false;
    }

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
            'Tests\CoreBundle\Fixture\LoadInstructorGroupData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadAlertChangeTypeData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function changeTypePutsToTest()
    {
        return [
            'room' => ['room', $this->getFaker()->text, AlertChangeTypeInterface::CHANGE_TYPE_LOCATION],
            'site' => ['site', $this->getFaker()->text, AlertChangeTypeInterface::CHANGE_TYPE_LOCATION],
            'startDate' => ['startDate', '1980-12-31T21:12:32+00:00', AlertChangeTypeInterface::CHANGE_TYPE_TIME],
            'endDate' => ['endDate', '1981-05-06T21:12:32+00:00', AlertChangeTypeInterface::CHANGE_TYPE_TIME],
            'learnerGroups' => ['learnerGroups', [1], AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP],
            'instructorGroups' => ['instructorGroups', [2], AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR],
            'learners' => ['learners', [1], AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP],
            'instructors' => ['instructors', [1], AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR],
        ];
    }

    public function putsToTest()
    {
        return [
            'session' => ['session', 2, AlertChangeTypeInterface::CHANGE_TYPE_LOCATION],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
            'updatedAt' => ['updatedAt', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[3, 4], ['id' => [4, 5]]],
            'room' => [[2], ['room' => 'room 3']],
            'site' => [[3], ['site' => 'site 4']],
            'session' => [[2, 3, 4], ['session' => 2]],
            'sessions' => [[2, 3, 4], ['sessions' => [2]]],
            'learnerGroups' => [[0], ['learnerGroups' => [1]]],
            'instructorGroups' => [[0], ['instructorGroups' => [1]]],
            'learners' => [[3], ['learners' => [2]]],
            'instructors' => [[5, 7], ['instructors' => [1]]],
            'courses' => [[0, 1, 2, 3, 4], ['courses' => [1]]],
        ];
    }

    protected function getTimeStampFields()
    {
        return ['updatedAt'];
    }

    /**
     * Allow dates to be skipped if required for a test
     * @inheritdoc
     */
    protected function compareData(array $expected, array $result)
    {
        if ($this->skipDates) {
            unset($expected['startDate']);
            unset($expected['endDate']);
            unset($result['startDate']);
            unset($result['endDate']);
        }

        return parent::compareData($expected, $result);
    }

    /**
     * Some of the offering time stamps are dynamic so we can't really test them
     * We have to skip that instead.
     */
    public function testGetAll()
    {
        $this->skipDates = true;
        $this->getAllTest();
    }

    /**
     * Some of the offering time stamps are dynamic so we can't really test them
     * We have to skip that instead.
     */
    public function filterTest(array $filters, array $expectedData)
    {
        $this->skipDates = true;
        parent::filterTest($filters, $expectedData);
    }

    public function testUpdatingLearnerGroupUpdatesOfferingStamp()
    {
        $dataLoader = $this->container->get(LearnerGroupData::class);
        $data = $dataLoader->getOne();
        $data['title'] = $this->getFaker()->text(20);
        $this->relatedTimeStampUpdateTest($data['offerings'][0], 'learnergroups', 'learnerGroup', $data);
    }

    public function testUpdatingInstructorGroupUpdatesOfferingStamp()
    {
        $dataLoader = $this->container->get(InstructorGroupData::class);
        $data = $dataLoader->getOne();
        $data['title'] = $this->getFaker()->text(20);
        $this->relatedTimeStampUpdateTest($data['offerings'][0], 'instructorgroups', 'instructorGroup', $data);
    }

    public function testUpdatingInstructorUpdatesOfferingStamp()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['instructors'] = ['1'];
        $this->relatedTimeStampUpdateTest($data['id'], 'offerings', 'offering', $data);
    }

    public function testUpdatingLearnerUpdatesOfferingStamp()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['learners'] = ['1'];
        $this->relatedTimeStampUpdateTest($data['id'], 'offerings', 'offering', $data);
    }

    public function testStartDateInSystemTimeZone()
    {
        $systemTimeZone = new \DateTimeZone(date_default_timezone_get());
        $now = new \DateTime('now', $systemTimeZone);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['startDate'] = $now->format('c');
        $postData = $data;
        $this->postTest($data, $postData);
    }

    public function testStartDateConvertedToSystemTimeZone()
    {
        $americaLa = new \DateTimeZone('America/Los_Angeles');
        $utc = new \DateTimeZone('UTC');
        $systemTimeZone = date_default_timezone_get();
        if ($systemTimeZone === 'UTC') {
            $systemTime = $utc;
            $now = new \DateTime('now', $americaLa);
        } else {
            $systemTime = $americaLa;
            $now = new \DateTime('now', $utc);
        }

        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $postData['startDate'] = $now->format('c');
        $data['startDate'] = $now->setTimezone($systemTime)->format('c');

        $this->postTest($data, $postData);
    }

    public function testEndDateInSystemTimeZone()
    {
        $systemTimeZone = new \DateTimeZone(date_default_timezone_get());
        $now = new \DateTime('now', $systemTimeZone);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['endDate'] = $now->format('c');
        $postData = $data;
        $this->postTest($data, $postData);
    }

    public function testEndDateConvertedToSystemTimeZone()
    {
        $americaLa = new \DateTimeZone('America/Los_Angeles');
        $utc = new \DateTimeZone('UTC');
        $systemTimeZone = date_default_timezone_get();
        if ($systemTimeZone === 'UTC') {
            $systemTime = $utc;
            $now = new \DateTime('now', $americaLa);
        } else {
            $systemTime = $americaLa;
            $now = new \DateTime('now', $utc);
        }

        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $postData['endDate'] = $now->format('c');
        $data['endDate'] = $now->setTimezone($systemTime)->format('c');

        $this->postTest($data, $postData);
    }
}
