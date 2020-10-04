<?php

namespace LocherCustomModelEntity\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="s_site_lochermodel")
 */
class LocherCustomModel extends ModelEntity {
    /**
     * @var integer $id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer $cms_id
     * @ORM\Column(type="integer", nullable=false)
     */
    private $cms_id;

    /**
     * @var string $name
     * @ORM\Column(type="string", length=500, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="html", type="string", nullable=false)
     */
    private $html;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $html
     */
    public function setHtml($html) {
        $this->html = $html;
    }

    /**
     * @return string
     */
    public function getHtml() {
        return $this->html;
    }

    /**
     * @return int
     */
    public function getCMSId()
    {
        return $this->cms_id;
    }

    /**
     * @param $cms_id
     */
    public function setCMSId($cms_id)
    {
        $this->cms_id = $cms_id;
    }
}