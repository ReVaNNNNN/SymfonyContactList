<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
    /**
     * @Route("/new")
     * @Template()
     * @Method("GET")
     */
    public function newAction()
    {
        $contact = new Contact();
        $form = $this->createForm(new ContactType(), $contact, ['action' => $this->generateUrl('app_contact_new')]);
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/new")
     * @Template()
     * @Method("POST")
     */
    public function createAction(Request $request)
    {   // tworzymy nowy obiekt na podstawie encji(klasy) Contact
        $contact = new Contact();
        // tworzymy formularz dzięki metodzie z klasy Controller
        $form = $this->createForm(new ContactType(), $contact);
        // UZUPEŁNIENIE KOMENTARZY
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            return $this->redirectToRoute('app_contact_showall'
                                       );
        }

        return ['form' => $form->createView()];
    }

    // DODAĆ WYSWIETELNIE 1 CONTACTU

    // wyświetlenie wszystkich obiektów z tabeli Contact
    /**
     * @Route("/show")
     * @Template()
     */
    public function showAllAction(Request $request)
    {
        $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->findAll();

        if(!$contact) {
            throw $this->createNotFoundException('There is no contact');
        }

        return ['contact' => $contact];
    }
    // edycja pojedyńczego obiektu z tabeli Contact
    /**
     * @Route("/{id}/modify")
     * @Template()
     */
    public function modifyAction(Request $request, $id)
    {
        $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->find($id);

        if(!$contact) {
            throw $this->createNotFoundException('There is no contact with '. $id . ' ID.');
        }

        $form = $this->createForm(new ContactType(), $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('app_contact_showall');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{id}/delete")
     * @Template()
     */     // usuwanie działą poprawnie natomiast zastanowić się nad formą tego usuwania, nie powinno to być
            // wyświetlone w formularzach, albo jako zwykly tekst albo od razu usuniecie + wyswietlenie komunikata
            // + moze przekierowanie ? albo widok z akcja na metodzie POST(wykona sie po kliknieciu DELETE)
    public function deleteAction(Request $request, $id)
    {
        $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->find($id);

        if(!$contact) {
            throw $this->createNotFoundException('There is no contact with '. $id . ' ID.');
        }

        $form = $this->createForm(new ContactType(), $contact);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($contact);
            $em->flush();

            return $this->redirectToRoute('app_contact_showall');
        }

        return ['form' => $form->createView()];
    }
}
