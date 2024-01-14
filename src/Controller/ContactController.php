<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/admin/contact', name: 'contact_admin')]
    public function index(ContactRepository $contactRepository) {
        
        return $this->render('admin/contact/index.html.twig',[
            'contact' => $contactRepository->findAll()
        ]);
    }


    #[Route('/contact', name: 'app_contact')]
    public function new(Request $request,EntityManagerInterface $manager): Response {
        $contact = new Contact();

        if ($this->getUser()) {
            $contact->setUsername($this->getUser()->getUsername())
                    ->setEmail($this->getUser()->getEmail());
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $manager->persist($contact);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre demande a été envoyé avec succès !'
            );

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_contact')]
    public function delete(ContactRepository $contactRepository, EntityManagerInterface $entityManager, $id) {
        
        $contactRepository = $entityManager->getRepository(Contact::class);
        $contact = $contactRepository->find($id);

        if (!$contact) {
            throw $this->createNotFoundException('No contact found for id '.$id);
        }

        $entityManager->remove($contact);
        $entityManager->flush();

        $this->addFlash(
        'success',
        'La demande a été supprimé avec succès. '
        );
        return $this->redirectToRoute('contact_admin');

    }

}
