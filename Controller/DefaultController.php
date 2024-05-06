<?php

namespace Aropixel\ContactBundle\Controller;

use Aropixel\AdminBundle\Services\Status;
use Aropixel\ContactBundle\Entity\Contact;
use Aropixel\ContactBundle\Form\ContactType;
use Aropixel\ContactBundle\Repository\ContactRepository;
use Aropixel\ContactBundle\Services\AttachmentProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("contact")]
class DefaultController extends AbstractController
{

    #[Route("/", name: "aropixel_contact_index", methods: ["GET"])]
    public function indexAction(ContactRepository $contactRepository) : Response
    {
        $contacts = $contactRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('@AropixelContact/Admin/index.html.twig', [
            'contacts' => $contacts
        ]);
    }


    #[Route("/{id}/read", name: "aropixel_contact_read", methods: ["GET"])]
    public function readAction(Status $status, Contact $contact) : Response
    {
        return $status
            ->setProperty('read')
            ->setValues(0, 1)
            ->changeStatus($contact)
            ;
    }


    #[Route("/{id}/answered", name: "aropixel_contact_answered", methods: ["GET"])]
    public function answeredAction(Status $status, Contact $contact) : Response
    {
        return $status
            ->setProperty('answered')
            ->setValues(0, 1)
            ->changeStatus($contact)
            ;
    }


    #[Route("/{id}/download/{file}", name: "aropixel_contact_download", methods: ["GET"])]
    public function downloadFile(AttachmentProvider $attachmentProvider, Contact $contact, $file) : Response
    {
        return $this->file($attachmentProvider->getAttachment($file));
    }


    #[Route("/{id}/edit", name: "aropixel_contact_edit", methods: ["GET", "POST"])]
    public function editAction(Request $request, Contact $contact, ContactRepository $contactRepository) : Response
    {
        $editForm = $this->createForm(ContactType::class, $contact);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $contactRepository->save($contact, true);
            $this->addFlash('notice', 'Votre contenu a bien été enregistré.');
            return $this->redirectToRoute('aropixel_contact_edit', ['id' => $contact->getId()]);
        }

        return $this->render('@AropixelContact/Admin/form.html.twig', [
            'contact' => $contact,
            'form' => $editForm->createView()
        ]);
    }

    #[Route("/{id}", name: "aropixel_contact_delete", methods: ["POST", "DELETE"])]
    public function deleteAction(Request $request, Contact $contact, ContactRepository $contactRepository) : Response
    {
        if ($this->isCsrfTokenValid('delete__contact'.$contact->getId(), $request->request->get('_token'))) {

            $contactRepository->remove($contact, true);

            $this->addFlash('notice', 'Le contact a bien été supprimé.');

        }

        return $this->redirectToRoute('aropixel_contact_index');
    }

}
