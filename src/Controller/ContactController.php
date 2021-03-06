<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use App\Event\ContactSentEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contactez-nous', name: 'contact')]
    public function index(Request $request, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher): Response
    {
        //Créer un objet contact
        $contact = new Contact();

        //Mapping de formulaire ContactType
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Création date d'envoi
            $contact->setSentAt(new \DateTime());

            //Envoyer dans la base de données
            $entityManager->persist($contact);
            $entityManager->flush();

            //Création event
            $event = new ContactSentEvent($contact);
            $eventDispatcher->dispatch($event);

            //Message de confirmation d'envoi
            $this->addFlash('success', 'Message envoyé');

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
