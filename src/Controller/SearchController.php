<?php

namespace App\Controller;

use App\Entity\TennisMatch;
use App\Form\SearchUserType;
use App\Form\SearchMatchType;
use App\Service\SearchService;
use App\Repository\UserRepository;
use App\Repository\TennisMatchRepository;
use App\Service\SearchMatchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/search", name="search_")
 */
class SearchController extends AbstractController
{
    /**
     * @Route("/", name="users")
     */
    public function search(Request $request, UserRepository $userRepository): Response
    {
        $search = new SearchService();
        $searchUserForm = $this->createForm(SearchUserType::class, $search);
        $searchUserForm->handleRequest($request);
//Old version that display the app.user (The code must be kept for now)
/*         if ($searchUserForm->isSubmitted() && $searchUserForm->isValid()) {
            $users = $userRepository->search($search);
        } else {
            $users = null;
        }
        return $this->render('search/index.html.twig', [
            'searchForm' => $searchUserForm->createView(),
            'users' => $users,
        ]); */

        if ($searchUserForm->isSubmitted() && $searchUserForm->isValid()) {
            $users = $userRepository->search($search);
            if (in_array($this->getUser(), $users)) {
                $keyToDestroy = array_search($this->getUser(), $users);
                unset($users[$keyToDestroy]);
            }
        } else {
            $users = null;
        }
        return $this->render('search/index.html.twig', [
            'searchForm' => $searchUserForm->createView(),
            'users' => $users,
        ]);
    }

    /**
     * @Route("/matchs", name="matches")
     */
    public function searchMatch(Request $request, TennisMatchRepository $tennisMatch): Response
    {
        $search = new SearchMatchService();
        $searchMatchForm = $this->createForm(SearchMatchType::class, $search);
        $searchMatchForm->handleRequest($request);
        if ($searchMatchForm->isSubmitted() && $searchMatchForm->isValid()) {
            $matchs = $tennisMatch->searchMatch($search);
        } else {
            $matchs = null;
        }
        return $this->render('tennis_match/search.html.twig', [
            'searchMatchForm' => $searchMatchForm->createView(),
            'matchs' => $matchs,
        ]);
    }
}
