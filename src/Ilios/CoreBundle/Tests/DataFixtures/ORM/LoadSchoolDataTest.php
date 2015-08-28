<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\ManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;

use Ilios\CoreBundle\Entity\Manager\SchoolManagerInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;

use Liip\FunctionalTestBundle\Test\WebTestCase;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadSchoolDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadSchoolDataTest extends WebTestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var resource
     */
    protected $dataFile;

    /**
     * @var ManagerInterface
     */
    protected $em;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->container = static::createClient()->getContainer();
        $this->loadFixtures($this->getFixtures());
        $this->loadDataFile($this->getDataFileName());
        $this->loadEntityManager($this->getEntityManagerServiceKey());
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(){
        fclose($this->dataFile);
    }

    /**
     * @param $fileName
     */
    protected function loadDataFile($fileName)
    {
        /**
         * @var FileLocator $fileLocator
         */
        $fileLocator = $this->container->get('file_locator');
        $path = $fileLocator->locate('@IliosCoreBundle/Resources/dataimport/' . basename($fileName));
        $this->dataFile = fopen($path, 'r');
    }

    /**
     * @param $serviceKey
     */
    protected function loadEntityManager($serviceKey)
    {
        $this->em = $this->container->get($serviceKey);
    }

    /**
     * @return string
     */
    public function getDataFileName()
    {
        return 'school.csv';
    }

    /**
     * @return string
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.school.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadSchoolData',
        ];
    }

    /**
     * @covers Ilios\CoreBundle\DataFixtures\ORM\LoadSchoolData::load
     */
    public function testLoad()
    {
        /**
         * @var SchoolManager $em
         */
        $em = $this->container->get('ilioscore.school.manager');

        $first = true;
        while (($data = fgetcsv($this->dataFile)) !== FALSE) {
            // step over the first row
            // since it contains the field names
            if ($first) {
                $first = false;
                continue;
            }
            $entity = $this->getEntityForRow($data);
            $this->assertDataIntegrity($data, $entity);
        }

    }

    /**
     * @param array $row
     * @return SchoolInterface
     */
    protected function getEntityForRow (array $row)
    {
        /**
         * @var SchoolManagerInterface $em
         */
        $em = $this->em;
        return $em->findSchoolBy(['id' => $row[0]]);

    }

    /**
     * @param array $row
     * @param SchoolInterface $entity
     */
    protected function assertDataIntegrity(array $row, $entity)
    {
        // `school_id`,`template_prefix`,`title`,`ilios_administrator_email`,`deleted`,`change_alert_recipients`
        $this->assertEquals($row[0], $entity->getId());
        $this->assertEquals($row[1], $entity->getTemplatePrefix());
        $this->assertEquals($row[2], $entity->getTitle());
        $this->assertEquals($row[3], $entity->getIliosAdministratorEmail());
        $this->assertEquals((boolean) $row[4], $entity->isDeleted());
        $this->assertEquals($row[5], $entity->getChangeAlertRecipients());
    }
}