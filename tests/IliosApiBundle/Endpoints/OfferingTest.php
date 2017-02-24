<?php

namespace Tests\IliosApiBundle\Endpoints;

use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * Offering API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class OfferingTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'offerings';

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
            'site' => ['site', $this->getFaker()->text],
            'session' => ['session', 2, AlertChangeTypeInterface::CHANGE_TYPE_LOCATION],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
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
            'startDate' => [[0], ['startDate' => 'test'], $skipped = true],
            'endDate' => [[0], ['endDate' => 'test'], $skipped = true],
            'updatedAt' => [[0], ['updatedAt' => 'test'], $skipped = true],
            'session' => [[2, 3, 4], ['session' => 2]],
            'learnerGroups' => [[0], ['learnerGroups' => [1]], $skipped = true],
            'instructorGroups' => [[0], ['instructorGroups' => [1]], $skipped = true],
            'learners' => [[0], ['learners' => [1]], $skipped = true],
            'instructors' => [[0], ['instructors' => [1]], $skipped = true],
        ];
    }

    protected function getTimeStampFields()
    {
        return ['updatedAt'];
    }

    /**
     * Some of the offering time stamps are dynamic so we can't really test them
     * @inheritdoc
     */
    protected function compareData(array $expected, array $result)
    {
        unset($expected['startDate']);
        unset($expected['endDate']);
        unset($result['startDate']);
        unset($result['endDate']);

        return parent::compareData($expected, $result);
    }

    /**
     * @dataProvider changeTypePutsToTest
     * @inheritdoc
     */
    public function testPut($key, $value, $changeType)
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        if (array_key_exists($key, $data) and $data[$key] == $value) {
            $this->fail(
                "This value is already set for {$key}. " .
                "Modify " . get_class($this) . '::putsToTest'
            );
        }
        //extract the ID before changing anything in case
        // the key we are changing is the ID
        $id = $data['id'];
        $data[$key] = $value;

        $postData = $data;
        $this->changeTypePutTest($data, $postData, $id, $changeType);
    }

    protected function changeTypePutTest(array $data, array $postData, $id, $expectedChangeType)
    {
        $this->putTest($data, $postData, $id, false);
        $this->checkAlertChange($id, $expectedChangeType, 2);
    }

    protected function checkAlertChange($id, $expectedChangeType, $instigator = null, $recipient = null)
    {
        $alerts = $this->getFiltered('alerts', 'alerts', [
            'filters[tableRowId]' => $id,
            'filters[tableName]' => 'offering',
            'filters[dispatched]' => '0'
        ]);
        $this->assertEquals(1, count($alerts));
        $alertData = $alerts[0];

        $this->assertEquals(count($alertData['changeTypes']), 1);
        $this->assertEquals($alertData['changeTypes'][0], $expectedChangeType);
        $this->assertEquals(count($alertData['instigators'][0]), 1);
        $this->assertEquals(count($alertData['recipients'][0]), 1);

        if ($instigator) {
            $this->assertEquals($instigator, $alertData['instigators'][0]);
        }
        if ($recipient) {
            $this->assertEquals($recipient, $alertData['recipients'][0]);
        }
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

    public function testUpdatingLearnerGroupUpdatesOfferingStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.learnergroup');
        $data = $dataLoader->getOne();
        $data['title'] = $this->getFaker()->text(20);
        $this->relatedTimeStampUpdateTest($data['offerings'][0], 'learnergroups', 'learnerGroup', $data);
    }

    public function testUpdatingInstructorGroupUpdatesOfferingStamp()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.instructorgroup');
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
}
