<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Entity\Alert;
use App\Entity\AlertChangeTypeInterface;
use App\Service\JsonWebTokenManager;
use App\Tests\DataLoader\InstructorGroupData;
use App\Tests\DataLoader\LearnerGroupData;
use App\Tests\DataLoader\ServiceTokenData;
use App\Tests\DataLoader\UserData;
use App\Tests\Fixture\LoadAlertChangeTypeData;
use App\Tests\Fixture\LoadIlmSessionData;
use App\Tests\Fixture\LoadInstructorGroupData;
use App\Tests\Fixture\LoadLearnerGroupData;
use App\Tests\Fixture\LoadOfferingData;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManager;

/**
 * Offering API endpoint Test.
 * @group api_1
 */
class OfferingTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'offerings';
    protected bool $skipDates = false;

    protected JsonWebTokenManager $jsonWebTokenManager;

    /**
     * Reset date skipping for each test
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->skipDates = false;
        /** @var JsonWebTokenManager $jsonWebTokenManager */
        $jsonWebTokenManager = $this->kernelBrowser->getContainer()->get(JsonWebTokenManager::class);
        $this->jsonWebTokenManager = $jsonWebTokenManager;
    }

    public function tearDown(): void
    {
        unset($this->jsonWebTokenManager);
        parent::tearDown();
    }

    protected function getFixtures(): array
    {
        return [
            LoadOfferingData::class,
            LoadLearnerGroupData::class,
            LoadInstructorGroupData::class,
            LoadIlmSessionData::class,
            LoadAlertChangeTypeData::class,
        ];
    }

    public static function changeTypePutsToTest(): array
    {
        return [
            'room' => ['room', 'room 101', AlertChangeTypeInterface::CHANGE_TYPE_LOCATION],
            'site' => ['site', 'main campus', AlertChangeTypeInterface::CHANGE_TYPE_LOCATION],
            'url' => ['url', 'https://lorem.ipsum', AlertChangeTypeInterface::CHANGE_TYPE_LOCATION],
            'startDate' => ['startDate', '1980-12-31T21:12:32+00:00', AlertChangeTypeInterface::CHANGE_TYPE_TIME],
            'endDate' => ['endDate', '1981-05-06T21:12:32+00:00', AlertChangeTypeInterface::CHANGE_TYPE_TIME],
            'learnerGroups' => ['learnerGroups', [1], AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP],
            'instructorGroups' => ['instructorGroups', [2], AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR],
            'learners' => ['learners', [1], AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP],
            'instructors' => ['instructors', [1], AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR],
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'session' => ['session', 3],
            'removeURL' => ['url', null],
            'remoteRoom' => ['room', null],
        ];
    }

    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
            'updatedAt' => ['updatedAt', 1, 99],
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[3, 4], ['id' => [4, 5]]],
            'room' => [[2], ['room' => 'room 3']],
            'site' => [[3], ['site' => 'site 4']],
            'url' => [[4], ['url' => 'https://example.com']],
            'session' => [[2, 3, 4], ['session' => 2]],
            'sessions' => [[2, 3, 4], ['sessions' => [2]]],
            'learnerGroups' => [[0], ['learnerGroups' => [1]]],
            'instructorGroups' => [[0], ['instructorGroups' => [1]]],
            'learners' => [[3], ['learners' => [2]]],
            'instructors' => [[5, 7], ['instructors' => [1]]],
            'courses' => [[0, 1, 2, 3, 4], ['courses' => [1]]],
            'schools' => [[0, 1, 2, 3, 4, 5, 6, 7], ['schools' => [1]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[3, 4], ['ids' => [4, 5]]];

        return $filters;
    }

    public function testGraphQL(): void
    {
        $this->skipDates = true;
        parent::testGraphQL();
    }

    /**
     * @dataProvider changeTypePutsToTest
     */
    public function testPutTriggerChangeType(string $key, $value, int $changeType): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        if (array_key_exists($key, $data) and $data[$key] == $value) {
            $this->fail(
                "This value is already set for $key. " .
                "Modify " . static::class . '::putsToTest'
            );
        }
        //extract the ID before changing anything in case
        // the key we are changing is the ID
        $id = $data['id'];
        $data[$key] = $value;

        $postData = $data;
        $this->putTest($data, $postData, $id, $jwt);
        $this->checkAlertChange($id, $changeType, UserData::ROOT_USER_ID, null, $data['id']);
    }

    /**
     * @dataProvider changeTypePutsToTest
     */
    public function testPatchJsonApiTriggerChangeType(string $key, $value, int $changeType): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $id = $data['id'];
        $data[$key] = $value;
        $jsonApiData = $dataLoader->createJsonApi($data);

        //When we remove a value in a test we shouldn't expect it back
        if (null === $value) {
            unset($data[$key]);
        }
        $this->patchJsonApiTest($data, $jsonApiData, $jwt);
        $this->checkAlertChange($id, $changeType, UserData::ROOT_USER_ID, null, 1);
    }

    public function testUpdatingLearnerGroupUpdatesOfferingStamp(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = self::getContainer()->get(LearnerGroupData::class);
        $data = $dataLoader->getOne();
        $data['title'] = 'lorem ipsum';
        $this->relatedTimeStampUpdateTest($data['offerings'][0], 'learnergroups', 'learnerGroup', $data, $jwt);
    }

    public function testUpdatingInstructorGroupUpdatesOfferingStamp(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = self::getContainer()->get(InstructorGroupData::class);
        $data = $dataLoader->getOne();
        $data['title'] = 'lorem ipsum';
        $this->relatedTimeStampUpdateTest($data['offerings'][0], 'instructorgroups', 'instructorGroup', $data, $jwt);
    }

    public function testUpdatingInstructorUpdatesOfferingStamp(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['instructors'] = ['1'];
        $this->relatedTimeStampUpdateTest($data['id'], 'offerings', 'offering', $data, $jwt);
    }

    public function testUpdatingLearnerUpdatesOfferingStamp(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['learners'] = ['1'];
        $this->relatedTimeStampUpdateTest($data['id'], 'offerings', 'offering', $data, $jwt);
    }

    public function testStartDateInSystemTimeZone(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $systemTimeZone = new DateTimeZone(date_default_timezone_get());
        $now = new DateTime('now', $systemTimeZone);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['startDate'] = $now->format('c');
        $postData = $data;
        $this->postTest($data, $postData, $jwt);
    }

    public function testStartDateConvertedToSystemTimeZone(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $americaLa = new DateTimeZone('America/Los_Angeles');
        $utc = new DateTimeZone('UTC');
        $systemTimeZone = date_default_timezone_get();
        if ($systemTimeZone === 'UTC') {
            $systemTime = $utc;
            $now = new DateTime('now', $americaLa);
        } else {
            $systemTime = $americaLa;
            $now = new DateTime('now', $utc);
        }

        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $postData['startDate'] = $now->format('c');
        $data['startDate'] = $now->setTimezone($systemTime)->format('c');

        $this->postTest($data, $postData, $jwt);
    }

    public function testEndDateInSystemTimeZone(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $systemTimeZone = new DateTimeZone(date_default_timezone_get());
        $now = new DateTime('now', $systemTimeZone);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['endDate'] = $now->format('c');
        $postData = $data;
        $this->postTest($data, $postData, $jwt);
    }

    public function testEndDateConvertedToSystemTimeZone(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $americaLa = new DateTimeZone('America/Los_Angeles');
        $utc = new DateTimeZone('UTC');
        $systemTimeZone = date_default_timezone_get();
        if ($systemTimeZone === 'UTC') {
            $systemTime = $utc;
            $now = new DateTime('now', $americaLa);
        } else {
            $systemTime = $americaLa;
            $now = new DateTime('now', $utc);
        }

        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $postData['endDate'] = $now->format('c');
        $data['endDate'] = $now->setTimezone($systemTime)->format('c');

        $this->postTest($data, $postData, $jwt);
    }

    /**
     * Some of the offering time stamps are dynamic, so we can't really test them
     * We have to skip that instead.
     */
    protected function runGetAllTest(string $jwt): void
    {
        $this->skipDates = true;
        parent::runGetAllTest($jwt);
    }

    /**
     * Some of the offering time stamps are dynamic, so we can't really test them
     * We have to skip that instead.
     */
    protected function filterTest(array $filters, array $expectedData, string $jwt): void
    {
        $this->skipDates = true;
        parent::filterTest($filters, $expectedData, $jwt);
    }


    /**
     * Look in the database for created alerts since they don't have an API endpoint to query
     */
    protected function checkAlertChange(
        int $id,
        int $expectedChangeType,
        ?int $userId,
        ?int $serviceTokenId,
        int $recipient
    ): void {
        /** @var EntityManager $entityManager */
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
        $userTokens = $alert->getServiceTokenInstigators();
        $recipients = $alert->getRecipients();
        $this->assertCount(1, $changeTypes);
        $this->assertEquals($changeTypes[0]->getId(), $expectedChangeType);


        $this->assertCount(1, $recipients);

        if ($userId) {
            $this->assertCount(1, $instigators);
            $this->assertEquals($userId, $instigators[0]->getId());
        }
        if ($serviceTokenId) {
            $this->assertCount(1, $userTokens);
            $this->assertEquals($serviceTokenId, $userTokens[0]->getId());
        }
        if ($recipient) {
            $this->assertEquals($recipient, $recipients[0]->getId());
        }
        $entityManager->close();
    }

    /**
     * Check for updated alerts in addition to other info
     */
    protected function postTest(array $data, array $postData, string $jwt): array
    {
        $responseData = parent::postTest($data, $postData, $jwt);
        //Instigator and school values are hard coded in test fixture data
        if ($this->jsonWebTokenManager->isUserToken($jwt)) {
            $this->checkAlertChange(
                $responseData['id'],
                AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING,
                UserData::ROOT_USER_ID,
                null,
                1
            );
        } else {
            $this->checkAlertChange(
                $responseData['id'],
                AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING,
                null,
                ServiceTokenData::ENABLED_SERVICE_TOKEN_ID,
                1
            );
        }

        return $responseData;
    }

    /**
     * Check for updated alerts in addition to other info
     */
    protected function postJsonApiTest(object $postData, array $data, string $jwt): array
    {
        $responseData = parent::postJsonApiTest($postData, $data, $jwt);
        //Instigator and school values are hard coded in test fixture data
        if ($this->jsonWebTokenManager->isUserToken($jwt)) {
            $this->checkAlertChange(
                $responseData['id'],
                AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING,
                UserData::ROOT_USER_ID,
                null,
                1
            );
        } else {
            $this->checkAlertChange(
                $responseData['id'],
                AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING,
                null,
                ServiceTokenData::ENABLED_SERVICE_TOKEN_ID,
                1
            );
        }

        return $responseData;
    }

    protected function getTimeStampFields(): array
    {
        return ['updatedAt'];
    }

    /**
     * Allow dates to be skipped if required for a test
     */
    protected function compareData(array $expected, array $result): void
    {
        if ($this->skipDates) {
            unset($expected['startDate']);
            unset($expected['endDate']);
            unset($result['startDate']);
            unset($result['endDate']);
        }

        parent::compareData($expected, $result);
    }

    /**
     * Allow dates to be skipped if required for a test
     */
    protected function compareGraphQLData(array $expected, object $result): void
    {
        if ($this->skipDates) {
            unset($expected['startDate']);
            unset($expected['endDate']);
            unset($result->startDate);
            unset($result->endDate);
        }
        parent::compareGraphQLData($expected, $result);
    }
}
