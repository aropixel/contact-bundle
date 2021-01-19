<?php
/**
 * Created by PhpStorm.
 * User: joelgomez
 * Date: 29/06/2017
 * Time: 12:35
 */

namespace Aropixel\ContactBundle\Services;

use Aropixel\ContactBundle\Entity\Contact;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;


class Sender
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var TwigEngine
     */
    private $templating;

    /**
     * @var string
     */
    private $template;

    /**
     * @var string
     */
    private $templateNotify;
    /**
     * @var string
     */
    private $subject;
    /**
     * @var string
     */
    private $senderEmail;
    /**
     * @var string
     */
    private $senderName;
    /**
     * @var array
     */
    private $bcc;
    /**
     * @var Form
     */
    private $form;
    /**
     * @var Contact
     */
    private $contact;

    private $data;

    /**
     * Sender constructor.
     * @param $em
     * @param $mailer
     */
    public function __construct(EntityManagerInterface $em, MailerInterface $mailer, Environment $templating, AttachmentProvider $attachmentProvider)
    {
        $this->bcc = array();
        $this->em = $em;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->attachmentProvider = $attachmentProvider;
    }


    /**
     * Sauvegarde le message en BDD, et envoie une notification à la personne concernée
     *
     * @param Contact $contact
     */
    public function saveAndSend(Contact $contact, $subject=false)
    {
        //
        if ($subject) {
            $this->subject = $subject;
        }

        //
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

            //
            if (count($this->bcc)) {
                $message->bcc($this->bcc);
            }

            //
            $this->mailer->send($message);

        }
        catch(TransportExceptionInterface $e){
//            dump($e);
        }

        return $this;
    }

    public function notify() {


        try{

            // Prépare le message HTML à envoyer
            $html = $this->templating->render($this->templateNotify, $this->data);

            $message = new Email();
            $message
                ->subject($this->subject)
                ->from(new Address($this->senderEmail, $this->senderName))
                ->to(new Address($this->contact->getEmailFrom(), $this->contact->getNomFrom()))
                ->html($html);

            //
            $this->mailer->send($message);

        }
        catch(TransportExceptionInterface $e){
        }


    }


    /**
     * Définit le formulaire associé à la prise de contact
     *
     * @param string $form
     */
    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * Définit le template Twig à utiliser pour l'envoi de la notification mail
     *
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Définit le template Twig à utiliser pour l'envoi de la notification de réponse automatique
     *
     * @param string $template
     */
    public function setTemplateNotify($template)
    {
        $this->templateNotify = $template;

        return $this;
    }

    /**
     * Définit les paramètres à passer au template Twig pour l'envoi de notification mail
     *
     * @param array $body
     *
     * @return Sender
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Définit l'email expéditeur de l'envoi de la notification mail
     *
     * @param string $senderEmail
     */
    public function setSenderEmail($senderEmail)
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * Définit le nom de l'expéditeur de l'envoi de la notification mail
     *
     * @param string $senderName
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
    }

    /**
     * Définit le sujet mail de la notification mail
     *
     * @param string $subject
     *
     * @return Sender
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Définit le ou les personnes à mettre en copie à la notification mail
     *
     * @param array $bcc
     *
     * @return Sender
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;

        return $this;
    }

}
