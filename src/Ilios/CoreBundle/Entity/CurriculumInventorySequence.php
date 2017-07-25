<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * Class CurriculumInventorySequence
 *
 * @ORM\Table(name="curriculum_inventory_sequence")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\CurriculumInventorySequenceRepository")
 *
 * @IS\Entity
 */
class CurriculumInventorySequence implements CurriculumInventorySequenceInterface
{
    use IdentifiableEntity;
    use DescribableEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="sequence_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    protected $id;

    /**
     * @var CurriculumInventoryReportInterface
     * @Assert\NotNull()
     *
     * @ORM\OneToOne(targetEntity="CurriculumInventoryReport", inversedBy="sequence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(
     *     name="report_id",
     *     referencedColumnName="report_id",
     *     unique=true,
     *     nullable=false,
     *     onDelete="cascade"
     *   )
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $report;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
    */
    protected $description;

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function setReport(CurriculumInventoryReportInterface $report)
    {
        $this->report = $report;
    }

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport()
    {
        return $this->report;
    }
}
