<?php

namespace App\tests\Controller;

use App\Entity\TennisMatch;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DomCrawler\Field\InputFormField;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserControllerTest extends WebTestCase
{

    public function testUserIndex()
    {  
        $client = static::createClient();  
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@monsite.com');
        $client->loginUser($testUser);
        
        $crawler = $client->request('GET', '/users');
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Recherche rapide de joueurs');
    }

    public function testUserNew()
    {   
        $client = static::createClient(); 
        $crawler = $client->request('GET', '/register');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Inscription');

        $email= 'test@test.com';
        $form = $crawler->selectButton('registration_form[save]')->form();
        $form['registration_form[email]'] = $email; 
        $form['registration_form[plainPassword][first]'] = 'test123';
        $form['registration_form[plainPassword][second]'] = 'test123';
        $form['registration_form[pseudo]'] = 'pseudotest';
        $form['registration_form[sex]'] = 'Homme';
        $form['registration_form[level]'] = 'Expert' ;
        $form['registration_form[city]'] = 'citytest';
        $form['registration_form[birthdate][day]'] = 2 ;
        $form['registration_form[birthdate][month]'] = 2 ;
        $form['registration_form[birthdate][year]'] = 2001 ;
        $form['registration_form[agreeTerms]'] = 1 ;
        
        $crawler= $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('user_index', $client->getRequest()->get('_route'));

        $em = self::$container->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'DESC']);
        $this->assertNotEquals($user->getPassword(), '');
        $this->assertEquals($user->getEmail(), $email);
    }

    public function testDeniedAccessUser()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('virginie.giraud@sfr.fr');
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/users/pseudotest/edit');
        $this->assertEquals(403, $client->getResponse()->getStatusCode()); 

        $crawler = $client->request('DELETE', '/users/pseudotest');
        $this->assertEquals(403, $client->getResponse()->getStatusCode()); 
        
        $crawler = $client->request('DELETE', '/users/pseudotest/deleteAvatar');
        $this->assertEquals(405, $client->getResponse()->getStatusCode()); 

        $crawler = $client->request('DELETE', '/users/pseudotest/editAvatar');
        $this->assertEquals(405, $client->getResponse()->getStatusCode()); 

        $crawler = $client->request('DELETE', '/users/update_password/pseudotest');
        $this->assertEquals(403, $client->getResponse()->getStatusCode()); 
    }
    
    public function testAccessUserWithAdmin()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@monsite.com');
        $client->loginUser($testUser);     

        $crawler = $client->request('GET', '/users/pseudotest/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());          
    }

    public function testUserDelete()
    {
        $client = static::createClient();  
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@test.com');
        $testUserId = $testUser->getId() ;
        $client->loginUser($testUser);

        $em = self::$container->get('doctrine');
        
        $crawler = $client->request('GET', '/users/pseudotest/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Supprimer mon compte')->form();

        $crawler= $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());        
        
        $em = self::$container->get('doctrine');
        $deletedUser = $em->getRepository(User::class)->find($testUserId);       
        $this->assertNull($deletedUser);
    }  

}