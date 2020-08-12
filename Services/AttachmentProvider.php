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
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Environment;


class AttachmentProvider
{

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * Sender constructor.
     * @param KernelInterface $kernel
     * @param ParameterBagInterface $parameterBag
     * @param SluggerInterface $slugger
     */
    public function __construct(KernelInterface $kernel, ParameterBagInterface $parameterBag, SluggerInterface $slugger)
    {
        $this->kernel = $kernel;
        $this->parameterBag = $parameterBag;
        $this->slugger = $slugger;
    }


    private function getAttachmentsPath($andCreate=false)
    {
        $dir = $this->parameterBag->get('aropixel_contact.attachment_dir');
        $path = $this->kernel->getProjectDir().'/'.$dir;

        if ($andCreate) {

            $filesystem = new Filesystem();
            try {
                $filesystem->mkdir($path);
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at ".$exception->getPath();
            }

        }

        return $path;
    }


    /**
     * Control attachment requirements: extension & max size
     *
     * @param File $file
     * @param string $extension
     * @param string $maxSize
     * @return bool|string
     */
    public function getAttachment($fileName)
    {
        return $this->getAttachmentsPath().'/'.$fileName;

    }


    /**
     * Control attachment requirements: extension & max size
     *
     * @param File $file
     * @param string $extension
     * @param string $maxSize
     * @return bool|string
     */
    public function uploadAttachment(File $file)
    {

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $path = $this->getAttachmentsPath(true);
        $file->move($path, $newFilename);


        return $newFilename;

    }

}
