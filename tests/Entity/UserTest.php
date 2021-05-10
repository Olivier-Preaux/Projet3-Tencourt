<?php

namespace App\tests\Entity;

use App\Entity\TennisMatch;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;
use DateTimeInterface;


class UserTest extends TestCase
{
    public function testIsTrue()
    {
        $user = new User();
        $date= new DateTime();
        $user2 = new User() ;
        $tennisMatch = new TennisMatch();
        $tennisMatch2 = new TennisMatch() ;

        $user   ->setEmail('email@test')
                ->setPassword('password')
                ->setPseudo('Pseudo')
                ->setSex('Homme')
                ->setLevel('Amateur')
                ->setAddress('1 rue du stade')
                ->setPostalCode('51100')
                ->setCity('Reims')
                ->setDescription('description')
                ->setPhone('1234567890')
                ->setBirthdate($date)
                ->setAvatar('image')
                ->setSlug('slug')
                ->setFirstName('firstname')
                ->setLastname('lastname')
                ->addFriend($user2)
                ->addTennisMatch($tennisMatch)
                ->addParticipationMatch($tennisMatch2);

        $this->assertTrue($user->getEmail() === 'email@test');
        $this->assertTrue($user->getPassword() === 'password');
        $this->assertTrue($user->getPseudo() === 'Pseudo');
        $this->assertTrue($user->getSex() === 'Homme');
        $this->assertTrue($user->getLevel() === 'Amateur');
        $this->assertTrue($user->getAddress() === '1 rue du stade');
        $this->assertTrue($user->getPostalCode() === '51100');
        $this->assertTrue($user->getCity() === 'Reims');
        $this->assertTrue($user->getDescription() === 'description');
        $this->assertTrue($user->getPhone() === '1234567890');
        $this->assertTrue($user->getBirthdate() === $date );
        $this->assertTrue($user->getAvatar() === 'image');
        $this->assertTrue($user->getSlug() === 'slug');
        $this->assertTrue($user->getFirstname() === 'firstname');
        $this->assertTrue($user->getLastname() === 'lastname');
        $this->assertContains( $user2 , $user->getFriend());
        $this->assertContains( $tennisMatch , $user->getTennisMatches());
        $this->assertContains( $tennisMatch2 , $user->getParticipationMatch());
    }

    public function testIsFalse()
    {
        $user = new User();
        $date= new DateTime();
        $user2 = new User() ;
        $tennisMatch = new TennisMatch();
        $tennisMatch2 = new TennisMatch() ;

        $user   ->setEmail('email@test')
                ->setPassword('password')
                ->setPseudo('Pseudo')
                ->setSex('Homme')
                ->setLevel('Amateur')
                ->setAddress('1 rue du stade')
                ->setPostalCode('51100')
                ->setCity('Reims')
                ->setDescription('description')
                ->setPhone('1234567890')
                ->setBirthdate($date)
                ->setAvatar('image')
                ->setSlug('slug')
                ->setFirstName('firstname')
                ->setLastname('lastname')
                ->addFriend($user2)
                ->addTennisMatch($tennisMatch)
                ->addParticipationMatch($tennisMatch2);

        $this->assertFalse($user->getEmail() === 'false');
        $this->assertFalse($user->getPassword() === 'false');
        $this->assertFalse($user->getPseudo() === 'false');
        $this->assertFalse($user->getSex() === 'false');
        $this->assertFalse($user->getLevel() === 'false');
        $this->assertFalse($user->getAddress() === 'false');
        $this->assertFalse($user->getPostalCode() === 'false');
        $this->assertFalse($user->getCity() === 'false');
        $this->assertFalse($user->getDescription() === 'false');
        $this->assertFalse($user->getPhone() === 'false');
        $this->assertFalse($user->getBirthdate() === new DateTime() );
        $this->assertFalse($user->getAvatar() === 'false');
        $this->assertFalse($user->getSlug() === 'false');
        $this->assertFalse($user->getFirstname() === 'false');
        $this->assertFalse($user->getLastname() === 'false');
        $this->assertNotContains( new User() , $user->getFriend());
        $this->assertNotContains( new TennisMatch , $user->getTennisMatches());
        $this->assertNotContains( new TennisMatch , $user->getParticipationMatch());
    }
}