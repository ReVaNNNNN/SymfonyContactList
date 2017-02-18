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

        $form = $this->createForm(new ContactType(), $contact,
                                    [                               // tutaj zmienic na new ?
                                    'action' => $this->generateUrl('app_contact_create')
                                    ]);
        // nie działa submit w url nowy po wpisaniu danych do formularza i probie wyslania ich
        // w ogole nie reaguje
        // dodac metode POST i URL new ponizej
        // natomiast poprawnie dodaje do bazy
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/create")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $contact = new Contact();

        $form = $this->createForm(new ContactType(), $contact);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            return $this->redirectToRoute('app_contact_show',
                                        [
                                            'id' => $contact->getId()
                                        ]);
        }

        return ['form' => $form->createView()];
    }

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
}
