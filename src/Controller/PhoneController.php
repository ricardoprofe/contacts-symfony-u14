<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Phone;
use App\Entity\Contact;

class PhoneController extends AbstractController
{
    #[Route('/phone/new/{id<\d+>}', name: 'new_phone')]
    public function newPhone(ManagerRegistry $doctrine, $id=''): Response
    {
        $contact = $doctrine->getRepository(Contact::class)->find($id);
        if($contact == null) {
            return $this->render('phone/new_edit_phone.html.twig', [
                'contact'=> $contact,
                'phone' => null,
                'page_title' => 'My Contacts App - New phone',
                'action' => 'Failed to add phone'
            ]);
        } else {
            $phone = new Phone();
            $phone->setIdContact($contact);
            $phone->setNumber("656565653");
            $phone->setType("Mobile");

            $entityManager = $doctrine->getManager();
            $entityManager->persist($phone);
            $entityManager->flush();

            return $this->render('phone/new_edit_phone.html.twig', [
                'phone' => $phone,
                'contact' => $contact,
                'page_title' => 'My Contacts App - New phone',
                'action' => 'Phone added'
            ]);
        }
    }

    #[Route('/phone/edit/{id<\d+>}/{number}', name: 'phone_edit')]
    public function updatePhone(ManagerRegistry $doctrine, $id='', $number=''): Response
    {
        $contact = $doctrine->getRepository(Contact::class)->find($id);
        if($contact == null) {
            return $this->render('phone/new_edit_phone.html.twig', [
                'contact'=> $contact,
                'phone' => null,
                'page_title' => 'My Contacts App - New phone',
                'action' => 'Failed to modify phone: no contact found'
            ]);
        } else {
            $entityManager = $doctrine->getManager();
            $phone = $doctrine->getRepository(Phone::class)->findOneBy(['number'=>$number, 'id_contact'=>$id]);
            if($phone) {
                $phone->setNumber("686868680");
                $entityManager->flush();
                $action = "Phone updated";
            } else {
                $action = "Failed to modify phone";
            }

            return $this->render('phone/new_edit_phone.html.twig', [
                'phone' => $phone,
                'contact' => $contact,
                'page_title' => 'My Contacts App - New phone',
                'action' => $action
            ]);
        }
    }

    #[Route('/phone/delete/{id<\d+>}/{number}', name: 'phone_delete')]
    public function deletePhone(ManagerRegistry $doctrine, $id='', $number=''): Response
    {
        $contact = $doctrine->getRepository(Contact::class)->find($id);
        if($contact == null) {
            return $this->render('phone/new_edit_phone.html.twig', [
                'contact'=> $contact,
                'phone' => null,
                'page_title' => 'My Contacts App - New phone',
                'action' => 'Failed to delete phone: no contact found'
            ]);
        } else {
            $entityManager = $doctrine->getManager();
            $phone = $doctrine->getRepository(Phone::class)->findOneBy(['number'=>$number, 'id_contact'=>$id]);
            if($phone) {
                $entityManager->remove($phone);
                $entityManager->flush();
                $action = "Phone deleted";
            } else {
                $action = "Failed to delete phone";
            }

            return $this->render('phone/new_edit_phone.html.twig', [
                'phone' => $phone,
                'contact' => $contact,
                'page_title' => 'My Contacts App - New phone',
                'action' => $action
            ]);
        }
    }


}
