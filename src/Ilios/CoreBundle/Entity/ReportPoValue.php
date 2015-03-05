<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ReportPoValue
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="report_po_value")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class ReportPoValue implements ReportPoValueInterface
{
    /**
     * @var ReportInterface
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Report", inversedBy="poValue")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="report_id", referencedColumnName="report_id", unique=true, onDelete="CASCADE")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $report;

    /**
     * @var string
     *
     * @ORM\Column(name="prepositional_object_table_row_id", type="string", length=14)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 14
     * )
     */
    protected $prepositionalObjectTableRowId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    protected $deleted;

    public function __construct()
    {
        $this->deleted = false;
    }

    /**
     * @param string $prepositionalObjectTableRowId
     */
    public function setPrepositionalObjectTableRowId($prepositionalObjectTableRowId)
    {
        $this->prepositionalObjectTableRowId = $prepositionalObjectTableRowId;
    }

    /**
     * @return string
     */
    public function getPrepositionalObjectTableRowId()
    {
        return $this->prepositionalObjectTableRowId;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param ReportInterface $report
     */
    public function setReport(ReportInterface $report)
    {
        $this->report = $report;
    }

    /**
     * @return ReportInterface
     */
    public function getReport()
    {
        return $this->report;
    }
}
