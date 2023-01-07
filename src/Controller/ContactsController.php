<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Contact;
use App\Entity\Phone;

class ContactsController extends AbstractController
{
    #[Route('/contact/{id<\d+>}', name: 'single_contact')]
    public function contact(ManagerRegistry $doctrine, $id=''): Response
    {
        $contact = $doctrine->getRepository(Contact::class)->find($id);

        return $this->render('contacts/contact.html.twig', [
            'contact' => $contact,
            'page_title' => 'My Contacts App - Contact'
        ]);
    }

    #[Route('/contact_list', name: 'contact_list')]
    public function contactList(ManagerRegistry $doctrine): Response
    {
        return $this->render('contacts/list.html.twig', [
            'contacts' => $doctrine->getRepository(Contact::class)->findAll(),
            'page_title' => 'My Contacts App - Contact List'
        ]);
    }

    #[Route('/contact/search/{search_string}', name: 'search_contact')]
    public function searchContact(ManagerRegistry $doctrine, $search_string=''): Response
    {
        return $this->render('contacts/list.html.twig', [
            'contacts' => $doctrine->getRepository(Contact::class)->findByNameOrSurname($search_string),
            'page_title' => 'My Contacts App - Search results'
        ]);
    }

    #[Route('/contact/new', name: 'new_contact')]
    public function newContact(ManagerRegistry $doctrine): Response {
        $contact = new Contact();
        $contact->setTitle("Mrs.");
        $contact->setName("Carlita");
        $contact->setSurname("Fontanares");
        $contact->setBirthdate(date_create("1980-01-30"));
        $contact->setEmail("carlafon@mail.com");

        $entityManager = $doctrine->getManager();
        //Uncomment this to add a new contact
        $entityManager->persist($contact);
        $entityManager->flush();

        $action = ($contact? 'New contact added' : 'Failed to add contact');

        return $this->render('contacts/new_edit_contact.html.twig', [
            'contact' => $contact,
            'page_title' => 'My Contacts App - New contact',
            'action' => $action
        ]);
    }
    #[Route('/contact/edit/{id<\d+>}', name: 'contact_edit')]
    public function updateContact(ManagerRegistry $doctrine, $id=''): Response {
        $entityManager = $doctrine->getManager();
        $contact = $doctrine->getRepository(Contact::class)->find($id);
        if($contact) {
            $action = "Contact updated";
            $contact->setName("New Name");
            //Uncomment this to modify the contact
            $entityManager->flush();
        } else {
            $action = "Failed to modify contact";
        }

        return $this->render('contacts/new_edit_contact.html.twig', [
            'contact' => $contact,
            'page_title' => 'My Contacts App - New contact',
            'action' => $action
        ]);
    }

    #[Route('/contact/delete/{id<\d+>}', name: 'contact_delete')]
    public function deleteContact(ManagerRegistry $doctrine, $id=''): Response {
        $entityManager = $doctrine->getManager();
        $contact = $doctrine->getRepository(Contact::class)->find($id);
        if($contact) {
            //Remove the phones
            $phones = $doctrine->getRepository(Phone::class)->findBy(['id_contact'=>$id]);
            foreach ($phones as $phone){
                $entityManager->remove($phone);
            }
            $entityManager->remove($contact);
            //Uncomment this to delete the contact
            $entityManager->flush();
            $action = "Contact deleted";
        } else {
            $action = "Failed to delete contact";
        }

        return $this->render('contacts/new_edit_contact.html.twig', [
            'contact' => $contact,
            'page_title' => 'My Contacts App - Delete contact',
            'action' => $action
        ]);
    }


}
