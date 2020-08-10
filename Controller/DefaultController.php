<?php

namespace Aropixel\ContactBundle\Controller;

use Aropixel\AdminBundle\Services\Status;
use Aropixel\ContactBundle\Entity\Contact;
use Aropixel\ContactBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contact controller.
 *
 * @Route("contact")
 */
class DefaultController extends AbstractController
{
    /**
     * Lists all contact entities.
     *
     * @Route("/", name="aropixel_contact_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Contact[] $contacts */
        $contacts = $em->getRepository(Contact::class)->findBy([], ['createdAt' => 'DESC']);

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


    /**
     * Change read proprety status.
     *
     * @Route("/{id}/read", name="aropixel_contact_read", methods={"GET"})
     */
    public function readAction(Status $status, Contact $contact)
    {
        return $status
            ->setProperty('read')
            ->setValues(0, 1)
            ->changeStatus($contact)
        ;

    }


    /**
     * Change read proprety status.
     *
     * @Route("/{id}/answered", name="aropixel_contact_answered", methods={"GET"})
     */
    public function answeredAction(Status $status, Contact $contact)
    {
        return $status
            ->setProperty('answered')
            ->setValues(0, 1)
            ->changeStatus($contact)
        ;

    }


    /**
     * Displays a form to edit an existing contact entity.
     *
     * @Route("/{id}/edit", name="aropixel_contact_edit", methods={"GET","POST"})
     */
    public function editAction(Request $request, Contact $contact)
    {
        $deleteForm = $this->createDeleteForm($contact);
        $editForm = $this->createForm(ContactType::class, $contact);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', 'Votre contenu a bien été enregistré.');

            return $this->redirectToRoute('aropixel_contact_edit', array('id' => $contact->getId()));
        }

        return $this->render('@AropixelContact/Admin/form.html.twig', array(
            'contact' => $contact,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a contact entity.
     *
     * @Route("/{id}", name="aropixel_contact_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Contact $contact)
    {
        $form = $this->createDeleteForm($contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($contact);
            $em->flush();
        }

        return $this->redirectToRoute('aropixel_contact_index');
    }

    /**
     * Creates a form to delete a contact entity.
     *
     * @param Contact $contact The contact entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(Contact $contact)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('aropixel_contact_delete', array('id' => $contact->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
