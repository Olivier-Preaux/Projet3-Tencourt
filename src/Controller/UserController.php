<?php

namespace App\Controller;

use App\Entity\Mail;
use App\Entity\User;
use App\Form\MailType;
use App\Form\UserType;
use App\Form\AvatarType;
use App\Service\Slugify;
use App\Entity\TennisMatch;
use App\Service\FileUploader;
use App\Form\UpdatePasswordType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TennisMatchRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/users", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        if (in_array($this->getUser(), $users)) {
            $keyToDestroy = array_search($this->getUser(), $users);
            unset($users[$keyToDestroy]);
        }

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader, Slugify $slugify): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($user->getPseudo() ?? '');
            $user->setSlug($slug);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{slug}", name="show", methods={"GET"})
     * @ParamConverter ("user", class="App\Entity\User", options={"mapping": {"slug": "slug"}})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{slug}/matches", name="matches", methods={"GET"})
     * @ParamConverter ("user", class="App\Entity\User", options={"mapping": {"slug": "slug"}})
     */
    public function showMatches(User $user): Response
    {
        $matches = $user->getTennisMatches();
        return $this->render('user/show_match.html.twig', [
            'user' => $user,
            'matches' => $matches
        ]);
    }

    /**
     * @Route("/{user}/matches/{tennisMatch}", name="match_show",
     * requirements={"user"="\d+", "tennisMatch"="\d+"}, methods={"GET"})
     */
    public function showMatch(TennisMatch $tennisMatch, User $user): Response
    {
        return $this->render('tennis_match/show.html.twig', [
            'tennis_match' => $tennisMatch,
            'user' => $user,
            ]);
    }

    /**
     * @Route("/{slug}/participations", name="participations_matches", methods={"GET"})
     * @ParamConverter ("user", class="App\Entity\User", options={"mapping": {"slug": "slug"}})
     */
    public function showParticipationsMatches(User $user, TennisMatchRepository $tennisMatchRepository): Response
    {
        $participationsMatches = $tennisMatchRepository->findParticipationMatch($user);
        return $this->render('user/participations_matches.html.twig', [
            'user' => $user,
            'participationsMatches' => $participationsMatches
        ]);
    }

    /**
     * @Route("/{slug}", name="profile", methods={"GET"})
     * @ParamConverter ("user", class="App\Entity\User", options={"mapping": {"slug": "slug"}})
     */
    public function myProfile(User $user): Response
    {
        $matches = $user->getTennisMatches();
        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'matches' => $matches
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="edit", methods={"GET","POST"})
     * @ParamConverter ("user", class="App\Entity\User", options={"mapping": {"slug": "slug"}})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Profil mis à jour avec succès !'
            );

            return $this->redirectToRoute('user_profile', ['slug' => $user->getSlug()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'editUserForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/editAvatar", name="edit_avatar", methods={"GET","POST"})
     */
    public function editAvatar(Request $request, User $user, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(AvatarType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                $avatarFileName = $fileUploader->upload($avatarFile);
                $user->setAvatar($avatarFileName);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            }
            $this->addFlash(
                'success',
                'Profile mis à jour avec succès !'
            );
            return $this->redirectToRoute('user_profile', ['slug' => $user->getSlug()]);
        }
        return $this->render('user/editAvatar.html.twig', [
            'user' => $user,
            'formAvatar' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="delete", methods={"DELETE"})
     * @ParamConverter ("user", class="App\Entity\User", options={"mapping": {"slug": "slug"}})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            if ($user->getAvatar() == true) {
                $fileToDelete = __DIR__ . '/../../public/uploads/' . $user->getAvatar();
                if (file_exists($fileToDelete)) {
                    unlink($fileToDelete);
                }
            }
        }
        $session = new Session();
        $session->invalidate();

        return $this->redirectToRoute('app_logout');
    }

    /**
     * @Route("/{slug}/deleteAvatar", name="delete_avatar", methods={"GET","POST"})
     * @ParamConverter ("user", class="App\Entity\User", options={"mapping": {"slug": "slug"}})
     */
    public function deleteAvatar(EntityManagerInterface $entityManager, User $user): Response
    {
        $fileToDelete = __DIR__ . '/../../public/uploads/' . $user->getAvatar();
        if (file_exists($fileToDelete)) {
            unlink($fileToDelete);
        }
        $user->setAvatar(null);
        $entityManager->flush();

        return $this->redirectToRoute('user_profile', ['slug' => $user->getSlug()]);
    }

    /**
     * @Route("/mail/{slug}", name="mail")
     * @ParamConverter ("user", class="App\Entity\User", options={"mapping": {"slug": "slug"}})
     */
    public function sendMail(Request $request, MailerInterface $mailer, User $user): Response
    {
            $form = $this->createForm(MailType::class);
            $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                $contact = [
                    $form->get('from')->getData(),
                    $form->get('to')->getData(),
                    $form->get('subject')->getData(),
                    $form->get('html')->getData()];

                $email = (new Email())
                    ->from($contact[0])
                    ->to($contact[1])
                    ->subject($contact[2])
                    ->html($contact[3]);

                $mailer->send($email);

                $this->addFlash('success', 'Votre e-mail a bien été envoyé');

                return $this->render('user/show.html.twig', [
                'user' => $user
                ]);
        }
        return $this->render(
            'emails/index.html.twig',
            [
            'form' => $form->createView(),
            'user' => $user
            ]
        );
    }

    /**
     * @Route("/update_password/{slug}", name="update_password")
     * @ParamConverter ("user", class="App\Entity\User", options={"mapping": {"slug": "slug"}})
     */
    public function updatePassword(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        User $user
    ): Response {

        $form = $this->createForm(UpdatePasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($passwordEncoder->isPasswordValid($user, $form->get('oldPassword')->getData())) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Votre mot de passe à bien été changé !');
                return $this->redirectToRoute('user_profile', ['slug' => $user->getSlug()]);
            } else {
                $form->addError(new FormError('Ancien mot de passe incorrect'));
            }
        }

        return $this->render('user/update_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ])
        ;
    }
}
