<?php

namespace App\tests\Controller;

use App\Entity\TennisMatch;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DomCrawler\Field\InputFormField;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MatchControllerTest extends WebTestCase
{
    public function testMatchIndex()
    {   
        $client = static::createClient(); 
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@monsite.com');
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    

    // public function testMatchNew()
    // {   
    //     $client = static::createClient();  
    //     $userRepository = static::$container->get(UserRepository::class);
    //     $testUser = $userRepository->findOneByEmail('admin@monsite.com');
    //     $client->loginUser($testUser);


    //     $crawler = $client->request('GET', '/tennis/match/admin/new');
    //     $this->assertEquals(200, $client->getResponse()->getStatusCode());

    //     $name= 'Mon match';
    //     $form = $crawler->selectButton('Créer le match')->form();
    //     $form['tennis_match[name]'] = $name;
    //     $form['tennis_match[level]'] = 'Expert' ;
    //     $form['tennis_match[eventDate]'] = "2020-01-01";
    //     $form['tennis_match[startHour]'] = "10:00";
    //     $form['tennis_match[endHour]'] = "12:00";
    //     $form['tennis_match[adress]'] = 'Troyes';
    //     $form['tennis_match[description]'] = 'Description';
        
    //     $crawler= $client->submit($form);

       
    //     $this->assertEquals(302, $client->getResponse()->getStatusCode());
    //     $crawler = $client->followRedirect();
    //     $this->assertEquals(200, $client->getResponse()->getStatusCode());
    //     $this->assertEquals('user_matches', $client->getRequest()->get('_route'));

    //     $em = self::$container->get('doctrine');
    //     $match = $em->getRepository(TennisMatch::class)->findOneBy([], ['id' => 'DESC']);
    //     $this->assertEquals($match->getName(), $name);       
    // }

    public function testMatchShow()
    {   
        $client = static::createClient();        
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@monsite.com');
        $client->loginUser($testUser);

        $em = self::$container->get('doctrine');
        $match = $em->getRepository(TennisMatch::class)->findOneBy([], ['id' => 'DESC']);
        $matchId = $match->getId() ;

        $crawler = $client->request('GET', '/tennis/match/'.$matchId );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('h1', 'Match de Administrator');
    }
    

    // public function testMatchEdit()
    // {
    //     $client = static::createClient();  
    //     $userRepository = static::$container->get(UserRepository::class);
    //     $testUser = $userRepository->findOneByEmail('admin@monsite.com');
    //     $client->loginUser($testUser);

    //     $em = self::$container->get('doctrine');
    //     $match = $em->getRepository(TennisMatch::class)->findOneBy([], ['id' => 'DESC']);
    //     $matchId = $match->getId() ;

    //     $crawler = $client->request('GET', '/tennis/match/matches/'.$matchId.'/edit');
    //     $this->assertEquals(200, $client->getResponse()->getStatusCode());

    //     $name = 'Nouveau Nom';
    //     $form = $crawler->selectButton('Mettre à jour')->form();
    //     $form['tennis_match[name]'] = $name; 

    //     $crawler= $client->submit($form);
    //     $this->assertEquals(302, $client->getResponse()->getStatusCode());
    //     $crawler = $client->followRedirect();
    //     $this->assertEquals(200, $client->getResponse()->getStatusCode());
    //     $this->assertEquals('tennis_match_show', $client->getRequest()->get('_route'));

    //     $em = self::$container->get('doctrine');
    //     $match = $em->getRepository(TennisMatch::class)->findOneBy([], ['id' => 'DESC']);
    //     $this->assertEquals($match->getName(), $name);       

    // }

    public function testDeniedMatchEdit()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('virginie.giraud@sfr.fr');
        $client->loginUser($testUser);

        $em = self::$container->get('doctrine');
        $match = $em->getRepository(TennisMatch::class)->findOneBy([], ['id' => 'DESC']);
        $matchId = $match->getId() ;

        $crawler = $client->request('DELETE', 'tennis/match/admin/ '.$matchId.'');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());         
    }

    public function testDeniedMatchDelete()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('virginie.giraud@sfr.fr');
        $client->loginUser($testUser);

        $em = self::$container->get('doctrine');
        $match = $em->getRepository(TennisMatch::class)->findOneBy([], ['id' => 'DESC']);
        $matchId = $match->getId() ;

        $crawler = $client->request('GET', '/tennis/match/matches/'.$matchId.'/edit');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());         
    }


    public function testMatchDelete()
    {
        $client = static::createClient();  
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@monsite.com');
        $client->loginUser($testUser);

        $em = self::$container->get('doctrine');
        $match = $em->getRepository(TennisMatch::class)->findOneBy([], ['id' => 'DESC']);
        $matchId = $match->getId() ;

        $crawler = $client->request('GET', '/tennis/match/'.$matchId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Supprimer le match')->form();

        $crawler= $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('user_matches', $client->getRequest()->get('_route'));

        $em = self::$container->get('doctrine');
        $deletedMatch = $em->getRepository(TennisMatch::class)->find($matchId);
       
        $this->assertNull($deletedMatch);
    }
}