<?php

namespace App\Controller;

use App\Form\AccountPasswordType;
use App\Form\AccountType;
use App\Repository\UserRepository;
use App\Service\FileUploader\User\Avatar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Account controller
 *
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    /**
     * Password hasher
     *
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    /**
     * Construct
     *
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Account page
     *
     * @Route("/", name="app_account")
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    /**
     * Account edit
     *
     * @Route("/edit", name="app_account_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, UserRepository $userRepository, Avatar $fileUploader): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatarRemove = $form->get('avatar_remove')->getData();
            if ($avatarRemove) {
                $user->setAvatar('');
            }
            $avatar = $form->get('avatar')->getData();
            if ($avatar) {
                $avatarFileName = $fileUploader->upload($avatar);
                $user->setAvatar($avatarFileName);
            }
            $userRepository->add($user);
            $this->addFlash(
                'success',
                'Account edited successfully!'
            );
            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/edit.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Account edit password
     *
     * @Route("/password", name="app_account_password", methods={"GET", "POST"})
     */
    public function password(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $userRepository->add($user);

            $this->addFlash(
                'success',
                'Password edited successfully!'
            );
            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/password.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Account owning bookings
     *
     * @Route("/owner-bookings", name="app_account_owner")
     */
    public function owner(): Response
    {
        return $this->render('account/owner.html.twig');
    }

    /**
     * Account membership bookings
     *
     * @Route("/member-bookings", name="app_account_member")
     */
    public function member(): Response
    {
        return $this->render('account/member.html.twig');
    }
}
