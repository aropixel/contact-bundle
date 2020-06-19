<?php

namespace Aropixel\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Form\Form;

/**
 * Contact
 *
 * @ORM\Table(name="aropixel_contact")
 * @ORM\Entity(repositoryClass="Aropixel\ContactBundle\Repository\ContactRepository")
 */
class Contact
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_from", type="string", length=255, nullable=true)
     */
    private $nomFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_to", type="string", length=255, nullable=true)
     */
    private $nomTo;

    /**
     * @var string
     *
     * @ORM\Column(name="email_from", type="string", length=255)
     */
    private $emailFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="email_to", type="string", length=255)
     */
    private $emailTo;

    /**
     * @var string
     *
     * @ORM\Column(name="objet", type="text", nullable=true)
     */
    private $objet;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(name="informations", type="array", nullable=true)
     */
    protected $informations;

    /**
     * @var bool
     *
     * @ORM\Column(name="readed", type="boolean")
     */
    protected $read;

    /**
     * @var bool
     *
     * @ORM\Column(name="answered", type="boolean")
     */
    protected $answered;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="answered_at", type="datetime", nullable=true)
     */
    private $answeredAt;


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
     * Set nomFrom
     *
     * @param string $nomFrom
     *
     * @return Contact
     */
    public function setNomFrom($nomFrom)
    {
        $this->nomFrom = $nomFrom;

        return $this;
    }

    /**
     * Get nomFrom
     *
     * @return string
     */
    public function getNomFrom()
    {
        return $this->nomFrom;
    }

    /**
     * Set nomTo
     *
     * @param string $nomTo
     *
     * @return Contact
     */
    public function setNomTo($nomTo)
    {
        $this->nomTo = $nomTo;

        return $this;
    }

    /**
     * Get nomTo
     *
     * @return string
     */
    public function getNomTo()
    {
        return $this->nomTo;
    }

    /**
     * Set emailFrom
     *
     * @param string $emailFrom
     *
     * @return Contact
     */
    public function setEmailFrom($emailFrom)
    {
        $this->emailFrom = $emailFrom;

        return $this;
    }

    /**
     * Get emailFrom
     *
     * @return string
     */
    public function getEmailFrom()
    {
        return $this->emailFrom;
    }

    /**
     * Set emailTo
     *
     * @param string $emailTo
     *
     * @return Contact
     */
    public function setEmailTo($emailTo)
    {
        $this->emailTo = $emailTo;

        return $this;
    }

    /**
     * Get emailTo
     *
     * @return string
     */
    public function getEmailTo()
    {
        return $this->emailTo;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Contact
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Contact
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set answeredAt
     *
     * @param \DateTime $answeredAt
     *
     * @return Contact
     */
    public function setAnsweredAt($answeredAt)
    {
        $this->answeredAt = $answeredAt;

        return $this;
    }

    /**
     * Get answeredAt
     *
     * @return \DateTime
     */
    public function getAnsweredAt()
    {
        return $this->answeredAt;
    }

    /**
     * Set read
     *
     * @param boolean $read
     *
     * @return Contact
     */
    public function setRead($read)
    {
        $this->read = $read;

        return $this;
    }

    /**
     * Get read
     *
     * @return boolean
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Set answered
     *
     * @param boolean $answered
     *
     * @return Contact
     */
    public function setAnswered($answered)
    {
        $this->answered = $answered;

        return $this;
    }

    /**
     * Get answered
     *
     * @return boolean
     */
    public function getAnswered()
    {
        return $this->answered;
    }



    /**
     * Set description
     *
     * @param string $description
     *
     * @return Contact
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
     * Set informations
     *
     * @param Form $form
     * @param array $fields
     *
     * @return Contact
     */
    public function setInformations(Form $form, $fields)
    {
        $formFields = $form->all();
        $data = array();

        // Parcours chaque champs du formulaire, et vérifie s'il fait partie des champs à sauver
        foreach ($formFields as $name => $child) {
            if (in_array($name, $fields)) {

                /** @var $child Form  */
                $config = $child->getConfig();

                $key = array_search($name, $fields);
                if (!is_numeric($key)) {
                    $label = $key;
                }
                else {
                    $label = $config->getOption("label");
                    if (!strlen($label)) {
                        $label = ucfirst($name);
                    }
                }

                $data[$label] = $child->getData();
            }
        }

        $this->informations = $data;

        return $this;
    }

    /**
     * Get informations
     *
     * @return array
     */
    public function getInformations()
    {
        return $this->informations;
    }

    /**
     * Set objet
     *
     * @param string $objet
     *
     * @return Contact
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get objet
     *
     * @return string
     */
    public function getObjet()
    {
        return $this->objet;
    }
}
