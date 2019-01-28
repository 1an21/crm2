<?php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Task
 *
 * @ORM\Table(name="tasks")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\TaskRepository")
 */
class Task
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $id;
    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="who_create", referencedColumnName="id")
     * })
     */
    private $whoCreate;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", length=65535, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_started", type="datetime", nullable=true)
     */
    private $dateStarted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_finished", type="datetime", nullable=true)
     */
    private $dateFinished;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", length=3, nullable=true)
     */
    private $status = 0;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Priority")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="priority", referencedColumnName="id")
     * })
     */
    private $priority;

    /**
     * @ORM\Column(name="images", type="string", nullable=true)
     *
     * @Assert\Image(mimeTypes={"image/*", "application/*"} )
     */
    private $image;

    /**
     * @var \AppBundle\Entity\Project
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     * })
     */
    private $project;
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="task")
     */
    protected $comments;

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Task
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateStarted
     *
     * @return Task
     */
    public function setDateStarted($dateStarted)
    {
        $this->dateStarted = $dateStarted;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateStarted()
    {
        return $this->dateStarted;
    }

    /**
     * Set dateUpdated
     *
     * @param \DateTime $dateUpdated
     *
     * @return Task
     */
    public function setDateFinished($dateFinished)
    {
        $this->dateFinished = $dateFinished;

        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return \DateTime
     */
    public function getDateFinished()
    {
        return $this->dateFinished;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Task
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set whoCreate
     *
     * @param \AppBundle\Entity\User $whoCreate
     *
     * @return Task
     */
    public function setWhoCreate(\AppBundle\Entity\User $whoCreate = null)
    {
        $this->whoCreate = $whoCreate;

        return $this;
    }

    /**
     * Get whoCreate
     *
     * @return \AppBundle\Entity\User
     */
    public function getWhoCreate()
    {
        return $this->whoCreate;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set priority
     *
     * @param \AppBundle\Entity\Priority $priority
     *
     * @return Priority
     */
    public function setPriority(\AppBundle\Entity\Priority $priority = null)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
    /**
     * Set project
     *
     * @param \AppBundle\Entity\Project $project
     *
     * @return Task
     */
    public function setProject(\AppBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \AppBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}

