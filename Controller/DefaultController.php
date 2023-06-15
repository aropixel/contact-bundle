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

        $delete_forms = array();
        foreach ($contacts as $entity) {
            $deleteForm = $this->createDeleteForm($entity);
            $delete_forms[$entity->getId()] = $deleteForm->createView();
        }

        return $this->render('@AropixelContact/Admin/index.html.twig', array(
            'contacts' => $contacts,
            'delete_forms' => $delete_forms,
        ));
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
        $deleteForm = $this->createDeleteForm($contact);
        $editForm = $this->createForm(ContactType::class, $contact);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $contactRepository->save($contact, true);
            $this->addFlash('notice', 'Votre contenu a bien été enregistré.');
            return $this->redirectToRoute('aropixel_contact_edit', array('id' => $contact->getId()));
        }

        return $this->render('@AropixelContact/Admin/form.html.twig', array(
            'contact' => $contact,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    #[Route("/{id}", name: "aropixel_contact_delete", methods: ["DELETE"])]
    public function deleteAction(Request $request, Contact $contact, ContactRepository $contactRepository) : Response
    {
        $form = $this->createDeleteForm($contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->remove($contact, true);
        }

        return $this->redirectToRoute('aropixel_contact_index');
    }

    private function createDeleteForm(Contact $contact) : FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('aropixel_contact_delete', array('id' => $contact->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
