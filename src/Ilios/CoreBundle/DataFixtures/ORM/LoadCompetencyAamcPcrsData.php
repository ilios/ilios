<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Ilios\CoreBundle\Entity\Competency;

/**
 * Class LoadCompetencyAamcPcrsData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadCompetencyAamcPcrsData extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('competency_x_aamc_pcrs', false);
    }
    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        // `competency_id`,`pcrs_id`
        /**
         * @var Competency $entity
         */
        $entity = $this->getReference('competency' . $data[0]);
        $entity->addAamcPcrs($this->getReference('aamc_pcrs' . $data[1]));
        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadAamcPcrsData',
            'Ilios\CoreBundle\DataFixtures\ORM\LoadCompetencyData',
        ];
    }
}
