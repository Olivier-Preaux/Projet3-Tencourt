<?php

namespace App\tests\Entity;

use App\Entity\TennisMatch;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;
use DateTimeInterface;


class TennisMatchTest extends TestCase
{
    public function testIsTrue()
    {

    $match = new TennisMatch();
    $datetime = new DateTime();
    $user = new User();
    $user2 = new User() ;

    $match  ->setName('Match du Lundi')
            ->setAdress('1 rue du stade')
            ->setDescription('un bon match')
            ->setStartHour($datetime)
            ->setEndHour($datetime)
            ->setEventDate($datetime)
            ->setOrganizer($user)
            ->addParticipent($user2)
            ->setLevel('Amateur');

    $this->assertTrue($match->getName() === 'Match du Lundi');
    $this->assertTrue($match->getAdress() === '1 rue du stade');
    $this->assertTrue($match->getDescription() === 'un bon match');
    $this->assertTrue($match->getStartHour() === $datetime );
    $this->assertTrue($match->getEndHour() === $datetime );
    $this->assertTrue($match->getEventDate() === $datetime );
    $this->assertTrue($match->getOrganizer() === $user );
    $this->assertContains( $user2 , $match->getParticipent());
    $this->assertTrue($match->getLevel() === 'Amateur' );
    }

    public function testIsFalse()
    {

    $match = new TennisMatch();
    $datetime = new DateTime();
    $user = new User();
    $user2 = new User() ;

    $match  ->setName('Match du Lundi')
            ->setAdress('1 rue du stade')
            ->setDescription('un bon match')
            ->setStartHour($datetime)
            ->setEndHour($datetime)
            ->setEventDate($datetime)
            ->setOrganizer($user)
            ->addParticipent($user2)
            ->setLevel('Amateur');

    $this->assertFalse($match->getName() === 'false');
    $this->assertFalse($match->getAdress() === 'false');
    $this->assertFalse($match->getDescription() === 'false');
    $this->assertFalse($match->getStartHour() === new DateTime() );
    $this->assertFalse($match->getEndHour() === new DateTime() );
    $this->assertFalse($match->getEventDate() === new DateTime() );
    $this->assertFalse($match->getOrganizer() === new User() );
    $this->assertNotContains( new User , $match->getParticipent());
    $this->assertFalse($match->getLevel() === 'false' );
    }
   
}
