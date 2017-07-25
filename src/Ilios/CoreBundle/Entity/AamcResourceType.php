<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\CategorizableEntity;
use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AamcResourceType
 *
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\AamcResourceTypeRepository")
 * @ORM\Table(name="aamc_resource_type")
 *
 * @IS\Entity
 */
class AamcResourceType implements AamcResourceTypeInterface
{
    use IdentifiableEntity;
    use DescribableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use CategorizableEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="resource_type_id", type="string", length=21)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 21
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200)

     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $title;

    /**
     * @ORM\Column(name="description", type="text")
     * @var string
     *
     * @Assert\NotBlank()
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
     * @var ArrayCollection|TermInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Term", mappedBy="aamcResourceTypes")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $terms;


    public function __construct()
    {
        $this->terms = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function addTerm(TermInterface $term)
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
            $term->addAamcResourceType($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeTerm(TermInterface $term)
    {
        if ($this->terms->contains($term)) {
            $this->terms->removeElement($term);
            $term->removeAamcResourceType($this);
        }
    }
}
