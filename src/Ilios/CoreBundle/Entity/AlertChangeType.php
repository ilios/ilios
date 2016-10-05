<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\AlertableEntity;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Entity\AlertInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class Alert
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="alert_change_type")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class AlertChangeType implements AlertChangeTypeInterface
{
    use TitledEntity;
    use StringableIdEntity;
    use IdentifiableEntity;
    use AlertableEntity;

    /**
     * @deprecated Replace with trait in 3.x
     * @var int
     *
     * @ORM\Column(name="alert_change_type_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
    * @ORM\Column(type="string", length=60)
    * @var string
    *
    * @Assert\NotBlank()
    * @Assert\Type(type="string")
    * @Assert\Length(
    *      min = 1,
    *      max = 60
    * )
    *
    * @JMS\Expose
    * @JMS\Type("string")
    */
    protected $title;

    /**
    * @var ArrayCollection|AlertInterface[]
    *
    * @ORM\ManyToMany(targetEntity="Alert", mappedBy="changeTypes")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    */
    protected $alerts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alerts = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function addAlert(AlertInterface $alert)
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->addChangeType($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeAlert(AlertInterface $alert)
    {
        if ($this->alerts->contains($alert)) {
            $this->alerts->removeElement($alert);
            $alert->removeChangeType($this);
        }
    }
}
