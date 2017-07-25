<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\SessionTypesEntity;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Entity\SessionTypeInterface;
use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class AamcMethod
 *
 * @ORM\Table(name="aamc_method")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\AamcMethodRepository")
 *
 * @IS\Entity
 */
class AamcMethod implements AamcMethodInterface
{
    use IdentifiableEntity;
    use DescribableEntity;
    use StringableIdEntity;
    use SessionTypesEntity;

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
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $sessionTypes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sessionTypes = new ArrayCollection();
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
