<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordEditFormType;
use App\Form\ProfileEditFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        // $user->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // $imageFile = $form->get('imageFile')->getData();
            // if ($imageFile) {
            //     // Your file handling logic, e.g., using VichUploaderBundle
            //     $user->setImageFile($imageFile);
            // }
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $this->addFlash(
                'success',
                'Le compte a étè créer avec succès. '
            );
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/profile/{username}', name: 'app_profile')]
    public function ShowProfile()
    {
        $user = $this->getUser();;
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('profile/show.html.twig', ['user' =>
        $this->getUser()]);
    }

    #[Route('/edit/profile/{id}', name: 'app_profile_edit')]
    public function editProfile(Request $request, UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ProfileEditFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le profile a été modifier avec succès. '
            );
            // Redirect to the profile page after successful edit
            return $this->redirectToRoute('app_profile', ['username' => $user->getUsername()]);
        }

        return $this->render('profile/edit.html.twig', [
            'profileEditForm' => $form->createView(),
        ]);
    }

    #[Route('/edit/password', name: 'app_password_edit')]
    public function editPassword(Request $request, UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(PasswordEditFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('password')->getData();

            if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
                // If old password is valid, update the password
                $user->setPassword($passwordEncoder->hashPassword($user, $newPassword));

                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash(
                    'success',
                    'Le mot de passe a été modifier avec succès. '
                );
                return $this->redirectToRoute('app_profile', ['username' => $user->getUsername()]);
            }
        }
        return $this->render('profile/edit_password.html.twig', [
            'passwordEditForm' => $form->createView(),
        ]);
    }
}
