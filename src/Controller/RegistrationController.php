<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Registration controller
 */
class RegistrationController extends AbstractController
{
    /**
     * Entity manager
     *
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * Password hasher
     *
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    /**
     * Construct
     *
     * @param ManagerRegistry $doctrine
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher)
    {
        $this->doctrine = $doctrine;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Registration page
     *
     * @Route("/registration", name="app_registration")
     */
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));

            $user->setRoles(['ROLE_USER']);
            $user->setAvatar('');

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
