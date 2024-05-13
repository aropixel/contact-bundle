<?php
/**
 * Created by PhpStorm.
 * User: joelgomez
 * Date: 29/06/2017
 * Time: 12:35
 */

namespace Aropixel\ContactBundle\Services;

use Aropixel\ContactBundle\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\Form;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;


class Sender
{

    private string $template;
    private string $templateNotify;
    private ?string $subject = null;
    private string $senderEmail;
    private string $senderName;
    private ?array $bcc = null;
    private Form $form;
    private Contact $contact;
    private $data;


    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MailerInterface $mailer,
        private readonly Environment $templating,
        private readonly AttachmentProvider $attachmentProvider
    ){
        $this->bcc = [];
    }


    /**
     * Sauvegarde le message en BDD, et envoie une notification à la personne concernée
     */
    public function saveAndSend(Contact $contact, $subject=false)
    {
        if ($subject) {
            $this->subject = $subject;
        }

        $this->contact = $contact;
        $this->contact->setRead(false);
        $this->contact->setAnswered(false);
        $this->em->persist($this->contact);
        $this->em->flush();

        // Prélève les données du formulaire pour les passer au template twig
        $fields = $this->form->all();
        $this->data = array();
        foreach ($fields as $name => $child) {
            $this->data[$name] = $child->getData();
        }

        try {

            // Prépare le message HTML à envoyer
            $html = $this->templating->render($this->template, $this->data);

            // Construit le mail à envoyer
            $message = new Email();
            $message
                ->subject($this->subject ?: $this->contact->getObjet())
                ->from(new Address($this->contact->getEmailFrom(), $this->contact->getNomFrom()))
                ->to(new Address($this->contact->getEmailTo(), $this->contact->getNomTo()))
                ->html($html)
            ;

            $attachments = $this->contact->getAttachments() ?: [];
            foreach ($attachments as $fileName) {

                $path = $this->attachmentProvider->getAttachment($fileName);
                $message->attachFromPath($path, $fileName);
            }

            foreach ($this->bcc as $key => $bcc) {
                if (0 === $key) {
                    $message->bcc($bcc);
                } else {
                    $message->addBcc($bcc);
                }
            }

            $this->mailer->send($message);

        } catch (TransportExceptionInterface $e){
//            dump($e);
        }

        return $this;
    }

    public function notify()
    {

        try {

            // Prépare le message HTML à envoyer
            $html = $this->templating->render($this->templateNotify, $this->data);

            $message = new Email();
            $message
                ->subject($this->subject)
                ->from(new Address($this->senderEmail, $this->senderName))
                ->to(new Address($this->contact->getEmailFrom(), $this->contact->getNomFrom()))
                ->html($html);

            $this->mailer->send($message);

        } catch (TransportExceptionInterface $e){}

    }


    /**
     * Définit le formulaire associé à la prise de contact
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Définit le template Twig à utiliser pour l'envoi de la notification mail
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Définit le template Twig à utiliser pour l'envoi de la notification de réponse automatique
     */
    public function setTemplateNotify(string $template)
    {
        $this->templateNotify = $template;

        return $this;
    }

    /**
     * Définit les paramètres à passer au template Twig pour l'envoi de notification mail
     */
    public function setBody($body) : Sender
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Définit l'email expéditeur de l'envoi de la notification mail
     */
    public function setSenderEmail(string $senderEmail)
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * Définit le nom de l'expéditeur de l'envoi de la notification mail
     */
    public function setSenderName(string $senderName)
    {
        $this->senderName = $senderName;
    }

    /**
     * Définit le sujet mail de la notification mail
     */
    public function setSubject(?string $subject = null) : Sender
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Définit le ou les personnes à mettre en copie à la notification mail
     */
    public function setBcc(?array $bcc = null) : Sender
    {
        $this->bcc = $bcc;

        return $this;
    }

}
