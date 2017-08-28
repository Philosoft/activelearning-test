<?php


namespace Todo\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Entity
 * @ORM\Table(name="task")
 */
class Task
{
    /**
     * @var int $id
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string $title
     * @ORM\Column(type="string", length=256, nullable=false)
     */
    protected $title;

    /**
     * @var string $description
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var \DateTime $created_at
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @var User
     * @ManyToOne(targetEntity="User", fetch="LAZY")
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $title
     * @return Task $this
     */
    public function setTitle($title = "")
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $description
     * @return Task $this
     */
    public function setDescription($description = "")
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $created_at
     * @return $this
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return string date in format Y-m-d H:s:i
     */
    public function getCreationDate()
    {
        return $this->getCreatedAt()->format("Y-m-d H:s:i");
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getDataAsArray()
    {
        return [
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            "description" => $this->getDescription(),
            "created_at" => $this->getCreationDate(),
        ];
    }

    /**
     * @param string $token
     * @return bool
     */
    public function checkAuthToken($token = "")
    {
        return $this->getUser()->getAuthToken() === $token;
    }
}
