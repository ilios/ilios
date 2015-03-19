<?php

namespace Ilios\CoreBundle\Entity\LearningMaterials;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Ilios\CoreBundle\Entity\LearningMaterial;

/**
 * Class File
 * @package Ilios\CoreBundle\Entity\LearningMaterials
 *
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class File extends LearningMaterial implements FileInterface
{
    /**
     * @var string
     * renamed from relative_file_system_location
     *
     * @ORM\Column(name="relative_file_system_location", type="string", length=128, nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 128
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $path;

    /**
     * renamed copyrightownership
     * @var boolean
     *
     * @ORM\Column(name="copyright_ownership", type="boolean", nullable=true)
     *
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("copyrightPermission")
     */
    protected $copyrightPermission;

    /**
    * @var string
    *
    * @ORM\Column(name="copyright_rationale", type="text", nullable=true)
    *
    * @Assert\Type(type="string")
    * @Assert\Length(
    *      min = 1,
    *      max = 65000
    * )
    *
    * @JMS\Expose
    * @JMS\Type("string")
    * @JMS\SerializedName("copyrightRationale")
    */
    protected $copyrightRationale;

    /**
    * @var string
    *
    * @ORM\Column(name="filename", type="string", length=255, nullable=true)
    *
    * @Assert\NotBlank()
    * @Assert\Type(type="string")
    * @Assert\Length(
    *      min = 1,
    *      max = 255
    * )
    *
    * @JMS\Expose
    * @JMS\Type("string")
    */
    protected $filename;

    /**
    * @var string
    *
    * @ORM\Column(name="mime_type", type="string", length=96, nullable=true)
    *
    * @Assert\NotBlank()
    * @Assert\Type(type="string")
    * @Assert\Length(
    *      min = 1,
    *      max = 96
    * )
    *
    * @JMS\Expose
    * @JMS\Type("string")
    * @JMS\SerializedName("mimetype")
    */
    protected $mimetype;

    /**
    * @var string
    *
    * @ORM\Column(name="filesize", type="integer", nullable=true, options={"unsigned"=true})
    *
    * @Assert\NotBlank()
    * @Assert\Type(type="integer")
    *
    * @JMS\Expose
    * @JMS\Type("integer")
    */
    protected $filesize;

    /**
     * @var UploadedFile;
     */
    protected $resource;

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param bool $copyrightPermission
     */
    public function setCopyrightPermission($copyrightPermission)
    {
        $this->copyrightPermission = $copyrightPermission;
    }

    /**
     * @return bool
     */
    public function hasCopyrightPermission()
    {
        return $this->copyrightPermission;
    }

    /**
     * @param string $copyrightRationale
     */
    public function setCopyrightRationale($copyrightRationale)
    {
        $this->copyrightRationale = $copyrightRationale;
    }

    /**
     * @return string
     */
    public function getCopyrightRationale()
    {
        return $this->copyrightRationale;
    }


    /**
     * @return string
     */
    public function getAbsolutePath()
    {
        return ($this->getResource() === null) ? null : $this->getUploadRootDir() . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
        return ($this->getResource() === null) ? null : $this->getUploadDir() . DIRECTORY_SEPARATOR;
    }

    /**
     * @todo: Figure out way to trigger upload through PrePersist by editing this property.
     * Perhaps a flag property managed by doctrine that we can change based on this.
     * @param UploadedFile $resource
     */
    public function setResource(UploadedFile $resource)
    {
        $this->setType(self::TYPE_FILE);
        $this->resource = $resource;
    }

    /**
     * @return UploadedFile|\SplFileInfo
     */
    public function getResource()
    {
        if ($this->resource === null && $this->path !== null) {
            return new \SplFileInfo($this->getAbsolutePath());
        }
        return $this->resource;
    }

    /**
     * @return void
     */
    public function upload()
    {
        if ($this->getResource() === null) {
            return;
        }

        $this->getResource()->move(
            $this->getUploadRootDir(),
            $this->getResource()->getClientOriginalName()
        );

        $this->path = $this->getResource()->getClientOriginalName();

        $this->resource = null;
    }

    /**
     * @return string
     */
    private function getUploadRootDir()
    {
        return __DIR__ . "/../../../../web" . $this->getUploadDir();
    }

    /**
     * @todo: Create magic file bucket sauce.
     * @return string
     */
    private function getUploadDir()
    {
        $uploadDir = 'uploads';
        return $uploadDir;
    }
}
