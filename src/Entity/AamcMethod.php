<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\SessionTypesEntity;
use App\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use App\Traits\ActivatableEntity;
use App\Traits\DescribableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;

/**
 * Class AamcMethod
 *
 * @ORM\Table(name="aamc_method")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\AamcMethodRepository")
 *
 * @IS\Entity
 */
class AamcMethod implements AamcMethodInterface
{
    use IdentifiableEntity;
    use DescribableEntity;
    use StringableIdEntity;
    use SessionTypesEntity;
    use ActivatableEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="method_id", type="string", length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 10
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $id;

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
     * @var ArrayCollection|SessionTypeInterface[]
     *
     * @ORM\ManyToMany(targetEntity="SessionType", mappedBy="aamcMethods")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $sessionTypes;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $active;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sessionTypes = new ArrayCollection();
        $this->active = true;
    }

    /**
     * @inheritdoc
     */
    public function addSessionType(SessionTypeInterface $sessionType)
    {
        if (!$this->sessionTypes->contains($sessionType)) {
            $this->sessionTypes->add($sessionType);
            $sessionType->addAamcMethod($this);
        }
    }

    /**
     * @param SessionTypeInterface $sessionType
     */
    public function removeSessionType(SessionTypeInterface $sessionType)
    {
        $this->sessionTypes->removeElement($sessionType);
        $sessionType->removeAamcMethod($this);
    }
}
