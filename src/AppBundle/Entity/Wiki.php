<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Wiki
 *
 * @ORM\Table(name="wiki")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\WikiRepository")
 */
class Wiki
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
     * @var string
     *
     * @ORM\Column(name="username", type="text", length=65535, nullable=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="text", length=65535, nullable=true)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="admin_url", type="text", length=65535, nullable=true)
     */
    private $adminUrl;


    /**
     * @var \AppBundle\Entity\Task
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project", referencedColumnName="id")
     * })
     */
    private $project;

    /**
     * @ORM\Column(name="file", type="string", nullable=true)
     *
     * @Assert\File(mimeTypes={ "text/*" , "image/*"})
     */
    private $file;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Set description
     *
     * @param string $username
     *
     * @return Task
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Task
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set adminUrl
     *
     * @param string $adminUrl
     *
     * @return Task
     */
    public function setAdminUrl($adminUrl)
    {
        $this->adminUrl = $adminUrl;

        return $this;
    }

    /**
     * Get adminUrl
     *
     * @return string
     */
    public function getAdminUrl()
    {
        return $this->adminUrl;
    }


    /**
     * Set task
     *
     * @param \AppBundle\Entity\Project $project
     *
     * @return Pause
     */
    public function setProject(\AppBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get task
     *
     * @return \AppBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

}
