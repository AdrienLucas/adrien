<?php

namespace SensioLabs\JobBoardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Sluggable\Util as Sluggable;

/**
 * Announcement.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SensioLabs\JobBoardBundle\Entity\AnnouncementRepository")
 */
class Announcement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     * @Assert\NotBlank(message="Job title should not be empty")
     */
    private $title;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=255)
     * @Assert\NotBlank(message="Company title should not be empty")
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=2)
     * @Assert\NotBlank(message="Country should not be empty")
     * @Assert\Country
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     * @Assert\NotBlank(message="City should not be empty")
     */
    private $city;

    /**
     * @var integer
     *
     * @ORM\Column(name="contractType", type="string", length=10)
     * @Assert\NotBlank(message="Contract type should be selected")
     * @Assert\Choice(callback="getContractTypes")
     */
    private $contractType;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank(message="Description should not be empty")
     */
    private $description;

    /**
     * @var string User uuid
     *             let it nullable to authorise anonymous users
     * @ORM\Column(name="user", type="text", nullable=true)
     */
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="valid", type="boolean")
     */
    private $valid = false;

    /**
     * @var string
     *
     * @ORM\Column(name="howToApply", type="text", nullable=true)
     */
    private $howToApply;

    /**
     * @var integer
     *
     * @ORM\Column(name="listViewsCount", type="integer")
     */
    private $listViewsCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="detailViewsCount", type="integer")
     */
    private $detailViewsCount = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endedAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public static function getContractTypes($onlyKeys = true)
    {
        $types = [
            'FULLTIME' => 'Full time',
            'PARTTIME' => 'Part time',
            'INTERNSHIP' => 'Internship',
            'FREELANCE' => 'Freelance',
            'ALTERNANCE' => 'Alternance',
        ];

        return $onlyKeys ? array_keys($types) : $types;
    }
    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Announcement
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set company.
     *
     * @param string $company
     *
     * @return Announcement
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company.
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set country.
     *
     * @param string $country
     *
     * @return Announcement
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city.
     *
     * @param string $city
     *
     * @return Announcement
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set contractType.
     *
     * @param integer $contractType
     *
     * @return Announcement
     */
    public function setContractType($contractType)
    {
        $this->contractType = $contractType;

        return $this;
    }

    /**
     * Get contractType.
     *
     * @return integer
     */
    public function getContractType()
    {
        return $this->contractType;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Announcement
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set user.
     *
     * @param string $user
     *
     * @return Announcement
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set howToApply.
     *
     * @param string $howToApply
     *
     * @return Announcement
     */
    public function setHowToApply($howToApply)
    {
        $this->howToApply = $howToApply;

        return $this;
    }

    /**
     * Get howToApply.
     *
     * @return string
     */
    public function getHowToApply()
    {
        return $this->howToApply;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug()
    {
        if (empty($this->slug)) {
            return Sluggable\Urlizer::urlize($this->title);
        } else {
            return $this->slug;
        }
    }

    public function getUrlParameters()
    {
        return [
            'country' => $this->getCountry(),
            'contractType' => $this->getContractType(),
            'slug' => $this->getSlug(),
        ];
    }

    /**
     * @return boolean
     */
    public function getValid()
    {
        return $this->valid;
    }

    /**
     * @param boolean $valid
     *
     * @return $this
     */
    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * @return int
     */
    public function getListViewsCount()
    {
        return $this->listViewsCount;
    }

    /**
     * @param int $listViewsCount
     *
     * @return $this
     */
    public function setListViewsCount($listViewsCount)
    {
        $this->listViewsCount = $listViewsCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getDetailViewsCount()
    {
        return $this->detailViewsCount;
    }

    /**
     * @param int $detailViewsCount
     *
     * @return $this
     */
    public function setDetailViewsCount($detailViewsCount)
    {
        $this->detailViewsCount = $detailViewsCount;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     *
     * @return $this
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndedAt()
    {
        return $this->endedAt;
    }

    /**
     * @param \DateTime $endedAt
     *
     * @return $this
     */
    public function setEndedAt($endedAt)
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    //Logical methods
    public function getViewsCount()
    {
        return $this->listViewsCount + $this->detailViewsCount;
    }

    /**
     * @return \DateTime
     */
    public function isPublished()
    {
        if (is_null($this->publishedAt) || is_null($this->endedAt)) {
            return false;
        } else {
            $now = new \DateTime('now');

            return  ($this->publishedAt <= $now &  $this->endedAt >= $now);
        }
    }

    /**
     * @return int
     */
    public function getTimeLeft()
    {
        if (is_null($this->endedAt)) {
            return false;
        } else {
            return date_diff($this->endedAt, new \DateTime());
        }
    }
}
