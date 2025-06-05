<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Course;
use App\Entity\MeshDescriptor;
use App\Entity\Session;
use App\Entity\SessionType;
use App\Entity\Term;
use App\Entity\User;
use App\Tests\DataLoader\SessionData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadSessionData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected SessionData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new Session();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);

            $properties = [
                'attireRequired' => 'setAttireRequired',
                'equipmentRequired' => 'setEquipmentRequired',
                'supplemental' => 'setSupplemental',
                'attendanceRequired' => 'setAttendanceRequired',
                'instructionalNotes' => 'setInstructionalNotes',
                'description' => 'setDescription',
            ];
            foreach ($properties as $key => $method) {
                if (array_key_exists($key, $arr)) {
                    $entity->$method($arr[$key]);
                }
            }
            $entity->setPublishedAsTbd($arr['publishedAsTbd']);
            $entity->setPublished($arr['published']);
            if (!empty($arr['sessionType'])) {
                $entity->setSessionType($this->getReference('sessionTypes' . $arr['sessionType'], SessionType::class));
            }
            if (!empty($arr['course'])) {
                $entity->setCourse($this->getReference('courses' . $arr['course'], Course::class));
            }
            foreach ($arr['terms'] as $id) {
                $entity->addTerm($this->getReference('terms' . $id, Term::class));
            }
            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id, MeshDescriptor::class));
            }
            foreach ($arr['administrators'] as $id) {
                $entity->addAdministrator($this->getReference('users' . $id, User::class));
            }
            foreach ($arr['studentAdvisors'] as $id) {
                $entity->addStudentAdvisor($this->getReference('users' . $id, User::class));
            }
            if (!empty($arr['postrequisite'])) {
                $ref = 'sessions' . $arr['postrequisite'];
                if ($this->hasReference($ref, Session::class)) {
                    $entity->setPostrequisite($this->getReference($ref, Session::class));
                }
            }
            foreach ($arr['prerequisites'] as $id) {
                $ref = 'sessions' . $id;
                if ($this->hasReference($ref, Session::class)) {
                    $entity->addPrerequisite($this->getReference($ref, Session::class));
                }
            }
            $manager->persist($entity);

            $this->addReference('sessions' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadSessionTypeData::class,
            LoadCourseData::class,
            LoadMeshDescriptorData::class,
            LoadTermData::class,
            LoadUserData::class,
        ];
    }
}
