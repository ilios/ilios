<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class MeshUserSelection
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="mesh_user_selection")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class MeshUserSelection implements MeshUserSelectionInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    /**
     * @var int
     *
     * @ORM\Column(name="mesh_user_selection_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="MeshDescriptor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(
     *     name="mesh_descriptor_uid",
     *     referencedColumnName="mesh_descriptor_uid",
     *     onDelete="CASCADE",
     *     nullable=false
     *   )
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("meshDescriptor")
     */
    protected $meshDescriptor;

    /**
    * @var string
    *
    * @ORM\Column(name="search_phrase", type="string", length=127, nullable=true)
    */
    protected $searchPhrase;

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     */
    public function setMeshDescriptor(MeshDescriptorInterface $meshDescriptor)
    {
        $this->meshDescriptor = $meshDescriptor;
    }

    /**
     * @return MeshDescriptorInterface
     */
    public function getMeshDescriptor()
    {
        return $this->meshDescriptor;
    }

    /**
     * @param string $searchPhrase
     */
    public function setSearchPhrase($searchPhrase)
    {
        $this->searchPhrase = $searchPhrase;
    }

    /**
     * @return string
     */
    public function getSearchPhrase()
    {
        return $this->searchPhrase;
    }
}
