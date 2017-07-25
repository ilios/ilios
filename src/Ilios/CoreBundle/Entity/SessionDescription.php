<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\SessionConsolidationEntity;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class SessionDescription
 *
 * @ORM\Table(name="session_description")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\SessionDescriptionRepository")
 *
 * @IS\Entity
 */
class SessionDescription implements SessionDescriptionInterface
{
    use IdentifiableEntity;
    use DescribableEntity;
    use StringableIdEntity;
    use SessionConsolidationEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="description_id", type="integer")
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
     * @var SessionInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\OneToOne(targetEntity="Session", inversedBy="sessionDescription")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(
     *     name="session_id",
     *     referencedColumnName="session_id",
     *     unique=true,
     *     nullable=false,
     *     onDelete="CASCADE"
     *   )
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $session;

    /**
     *
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
     * @IS\RemoveMarkup
     *
    */
    protected $description;

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function getSession()
    {
        return $this->session;
    }
}
