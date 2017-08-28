<?php


namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

class User
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="auto")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=256, nullable=false)
     */
    protected $name;
}