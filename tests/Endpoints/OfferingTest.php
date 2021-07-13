<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Entity\Alert;
use App\Entity\AlertChangeTypeInterface;
use App\Tests\DataLoader\InstructorGroupData;
use App\Tests\DataLoader\LearnerGroupData;
use App\Tests\Fixture\LoadAlertChangeTypeData;
use App\Tests\Fixture\LoadIlmSessionData;
use App\Tests\Fixture\LoadInstructorGroupData;
use App\Tests\Fixture\LoadLearnerGroupData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\ReadWriteEndpointTest;

/**
 * Offering API endpoint Test.
 * @group api_1
 * @group time-sensitive
 */
class OfferingTest extends ReadWriteEndpointTest
{
    protected string $testName =  'offerings';
    protected $skipDates = false;

    /**
     * Reset date skipping for each test
     */
    public function setUp(): void
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
            LoadOfferingData::class,
            LoadLearnerGroupData::class,
            LoadInstructorGroupData::class,
            LoadIlmSessionData::class,
            LoadAlertChangeTypeData::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function changeTypePutsToTest()
    {
        return [
            'room' => ['room', $this->getFaker()->text(), AlertChangeTypeInterface::CHANGE_TYPE_LOCATION],
            'site' => ['site', $this->getFaker()->text(), AlertChangeTypeInterface::CHANGE_TYPE_LOCATION],
            'url' => ['url', $this->getFaker()->url(), AlertChangeTypeInterface::CHANGE_TYPE_LOCATION],
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
            'session' => ['session', 3],
            'removeURL' => ['url', null],
            'remoteRoom' => ['room', null],
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
            'url' => [[4], ['url' => 'http://example.com']],
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
     * @dataProvider changeTypePutsToTest
     */
    public function testPutTriggerChangeType(string $key, $value, int $changeType)
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        if (array_key_exists($key, $data) and $data[$key] == $value) {
            $this->fail(
                "This value is already set for {$key}. " .
                "Modify " . $this::class . '::putsToTest'
            );
        }
        //extract the ID before changing anything in case
        // the key we are changing is the ID
        $id = $data['id'];
        $data[$key] = $value;

        $postData = $data;
        $this->putTest($data, $postData, $id, false);
        $this->checkAlertChange($id, $changeType, 2, 1);
    }

    /**
     * @dataProvider changeTypePutsToTest
     */
    public function testPatchJsonApiTriggerChangeType(string $key, $value, int $changeType)
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $id = $data['id'];
        $data[$key] = $value;
        $jsonApiData = $dataLoader->createJsonApi($data);

        //When we remove a value in a test we shouldn't expect it back
        if (null === $value) {
            unset($data[$key]);
        }
        $this->patchJsonApiTest($data, $jsonApiData);
        $this->checkAlertChange($id, $changeType, 2, 1);
    }

    /**
     * Look in the database for created alerts since the don't have an API endpoint to query
     */
    protected function checkAlertChange(int $id, int $expectedChangeType, int $instigator, int $recipient)
    {
        $entityManager = $this->kernelBrowser->getContainer()
            ->get('doctrine')
            ->getManager();

        /** @var Alert[] $alerts */
        $alerts = $entityManager
            ->getRepository(Alert::class)
            ->findBy([
                'tableRowId' => $id,
                'tableName' => 'offering',
                'dispatched' => '0',
            ]);
        $this->assertIsArray($alerts);
        $this->assertCount(1, $alerts, "Alert was returned");
        $alert = $alerts[0];

        $changeTypes = $alert->getChangeTypes();
        $instigators = $alert->getInstigators();
        $recipients = $alert->getRecipients();
        $this->assertCount(1, $changeTypes);
        $this->assertEquals($changeTypes[0]->getId(), $expectedChangeType);

        $this->assertCount(1, $instigators);
        $this->assertCount(1, $recipients);

        if ($instigator) {
            $this->assertEquals($instigator, $instigators[0]->getId());
        }
        if ($recipient) {
            $this->assertEquals($recipient, $recipients[0]->getId());
        }
        $entityManager->close();
    }

    /**
     * Check for updated alerts in addition to other info
     * @inheritdoc
     */
    protected function postTest(array $data, array $postData)
    {
        $responseData = parent::postTest($data, $postData);
        //Instigator and school values are hard coded in test fixture data
        $this->checkAlertChange(
            $responseData['id'],
            AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING,
            $instigator = 2,
            $school = 1
        );

        return $responseData;
    }

    /**
     * Check for updated alerts in addition to other info
     * @inheritdoc
     */
    protected function postJsonApiTest(object $postData, array $data)
    {
        $responseData = parent::postJsonApiTest($postData, $data);
        //Instigator and school values are hard coded in test fixture data
        $this->checkAlertChange(
            $responseData['id'],
            AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING,
            $instigator = 2,
            $school = 1
        );

        return $responseData;
    }

    /**
     * Some of the offering time stamps are dynamic so we can't really test them
     * We have to skip that instead.
     * @param array $filters
     * @param array $expectedData
     * @param int $userId
     */
    public function filterTest(array $filters, array $expectedData, int $userId = 2)
    {
        $this->skipDates = true;
        parent::filterTest($filters, $expectedData, $userId);
    }

    public function testUpdatingLearnerGroupUpdatesOfferingStamp()
    {
        $dataLoader = self::getContainer()->get(LearnerGroupData::class);
        $data = $dataLoader->getOne();
        $data['title'] = $this->getFaker()->text(50);
        $this->relatedTimeStampUpdateTest($data['offerings'][0], 'learnergroups', 'learnerGroup', $data);
    }

    public function testUpdatingInstructorGroupUpdatesOfferingStamp()
    {
        $dataLoader = self::getContainer()->get(InstructorGroupData::class);
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
