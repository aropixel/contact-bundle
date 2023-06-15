<?php

namespace Aropixel\ContactBundle\Entity;

use Aropixel\ContactBundle\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Form\Form;

#[ORM\Table(name: "aropixel_contact")]
#[ORm\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "nom_from", type: "string", length: 255, nullable: true)]
    private ?string $nomFrom;

    #[ORM\Column(name: "nom_to", type: "string", length: 255, nullable: true)]
    private ?string $nomTo;

    #[ORM\Column(name: "email_from", type: "string", length: 255)]
    private string $emailFrom;

    #[ORM\Column(name: "email_to", type: "string", length: 255)]
    private string $emailTo;

    #[ORM\Column(name: "objet", type: "text", nullable: true)]
    private ?string $objet;

    #[ORM\Column(name: "message", type: "text", nullable: true)]
    private ?string $message;

    #[ORM\Column(name: "description", type: "text", nullable: true)]
    protected ?string $description;

    #[ORM\Column(name: "informations", type: "json", nullable: true)]
    protected ?array $informations;

    #[ORM\Column(name: "attachments", type: "json", nullable: true)]
    protected ?array $attachments;

    #[ORM\Column(name: "readed", type: "boolean")]
    protected bool $read;

    #[ORm\Column(name: "answered", type: "boolean")]
    protected bool $answered;

    #[Gedmo\Timestampable(on: "create")]
    #[ORM\Column(name: "created_at", type: "datetime", nullable: true)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(name: "answered_at", type: "datetime", nullable: true)]
    private ?\DateTime $answeredAt = null;


    public function getId(): int
    {
        return $this->id;
    }

    public function setNomFrom(string $nomFrom) : Contact
    {
        $this->nomFrom = $nomFrom;

        return $this;
    }

    public function getNomFrom() : string
    {
        return $this->nomFrom;
    }

    public function setNomTo(string $nomTo) : Contact
    {
        $this->nomTo = $nomTo;

        return $this;
    }

    public function getNomTo() : string
    {
        return $this->nomTo;
    }

    public function setEmailFrom(string $emailFrom) : Contact
    {
        $this->emailFrom = $emailFrom;

        return $this;
    }

    public function getEmailFrom() : string
    {
        return $this->emailFrom;
    }

    public function setEmailTo(string $emailTo) : Contact
    {
        $this->emailTo = $emailTo;

        return $this;
    }

    public function getEmailTo() : string
    {
        return $this->emailTo;
    }

    public function setMessage(string $message) : Contact
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage() : string
    {
        return $this->message;
    }

    public function setCreatedAt(\DateTime $createdAt) : Contact
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt() : \DateTime
    {
        return $this->createdAt;
    }

    public function setAnsweredAt(\DateTime $answeredAt) : Contact
    {
        $this->answeredAt = $answeredAt;

        return $this;
    }

    public function getAnsweredAt() : \DateTime
    {
        return $this->answeredAt;
    }

    public function setRead(bool $read) : Contact
    {
        $this->read = $read;

        return $this;
    }

    public function getRead() : bool
    {
        return $this->read;
    }

    public function setAnswered(bool $answered) : Contact
    {
        $this->answered = $answered;

        return $this;
    }

    public function getAnswered() : bool
    {
        return $this->answered;
    }



    public function setDescription(string $description) : Contact
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function setInformations(array $informations, array $fields=null) : Contact
    {
        if (is_null($fields)) {
            $this->informations = $informations;
            }

        $form = $informations;
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

    public function getInformations() : array
    {
        return $this->informations;
    }

    public function getAttachments() : ?array
    {
        return $this->attachments;
    }

    public function addAttachment($title, string $filename) : Contact
    {
        $this->attachments[$title] = $filename;
        return $this;
    }

    public function setObjet(string $objet) : Contact
    {
        $this->objet = $objet;

        return $this;
    }

    public function getObjet() : string
    {
        return $this->objet;
    }
}
