<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\TennisMatch;
use App\Form\TennisMatchType;
use App\Repository\TennisMatchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use function Amp\Iterator\toArray;

/**
 * @Route("/tennis/match")
 */
class TennisMatchController extends AbstractController
{
    /**
     * @Route("/", name="tennis_match_index", methods={"GET"})
     */
    public function index(TennisMatchRepository $tennisMatchRepository): Response
    {
        return $this->render('tennis_match/index.html.twig', [
            'tennis_matches' => $tennisMatchRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{slug}/new", name="tennis_match_new", methods={"GET","POST"})
     */
    public function new(Request $request, string $slug): Response
    {
        $tennisMatch = new TennisMatch();
        $form = $this->createForm(TennisMatchType::class, $tennisMatch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $tennisMatch->setOrganizer($this->getUser());
            $tennisMatch->addParticipent($this->getUser());
            $entityManager->persist($tennisMatch);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Votre match a bien été ajouté!'
            );

            return $this->redirectToRoute('user_matches', [
                'slug' => $slug,
            ]);
        }

        return $this->render('tennis_match/new.html.twig', [
            'tennis_match' => $tennisMatch,
            'tennismatchform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tennis_match_show", methods={"GET"})
     */
    public function show(TennisMatch $tennisMatch): Response
    {
        $user = new User();
        $participents = $tennisMatch->getParticipent();
        $user = $tennisMatch->getOrganizer();
        $nbParticipents = count($participents);

        $isParticipent = false;
        if (in_array($this->getUser(), $participents->getValues())) {
            $isParticipent = true;
        }

        return $this->render('tennis_match/show.html.twig', [
            'tennis_match' => $tennisMatch,
            'nbParticipents' => $nbParticipents,
            'isParticipent' => $isParticipent,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/matches/{id}/edit", name="tennis_match_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TennisMatch $tennisMatch): Response
    {
        $form = $this->createForm(TennisMatchType::class, $tennisMatch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'Votre match a bien été modifié'
            );

            return $this->redirectToRoute('tennis_match_show', [
                'id' => $tennisMatch->getId()
            ]);
        }

        return $this->render('tennis_match/edit.html.twig', [
            'tennis_match' => $tennisMatch,
            'formEditMatch' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/{id}", name="tennis_match_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TennisMatch $tennisMatch, string $slug): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tennisMatch->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tennisMatch);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Votre match a bien été supprimé'
            );
        }
        return $this->redirectToRoute('user_matches', ['slug' => $slug,]);
    }

    /**
     * @Route("/{id}/participent", name="tennis_match_add")
     */
    public function addToMatch(TennisMatch $match, EntityManagerInterface $em): Response
    {
        $match->addParticipent($this->getUser());

        $em->flush();

        $this->addFlash(
            'success',
            'Votre participation a bien été prise en compte !'
        );

        return $this->redirectToRoute('tennis_match_show', [
            'id' => $match->getId(),
        ]);
    }

    /**
     * @Route("/{id}/remove", name="tennis_match_remove")
     */
    public function removeFromTheMatch(TennisMatch $match, EntityManagerInterface $em): Response
    {
        $match->removeParticipent($this->getUser());

        $em->flush();

        $this->addFlash(
            'success',
            'Vous ne participez plus à ce match !'
        );

        return $this->redirectToRoute('tennis_match_show', ['id' => $match->getId()]);
    }
}
