<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\TennisMatch;
use App\DataFixtures\UserFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TennisMatchFixtures extends Fixture implements DependentFixtureInterface
{
    private $cities = [
        'Paris',
        'Reims',
        'Epernay',
        'Bordeaux',
        'Lyon',
        'Toulouse',
        'Marseille',
        'Brest',
        'Lille',
    ];

    public function getDependencies()
    {
        return [UserFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($userReferenceNumber = 0; $userReferenceNumber < 10; $userReferenceNumber++) {
            for ($i = 0; $i < 1; $i++) {
                $match = new TennisMatch();
                $match->setLevel($faker->randomElement(array (
                    'Débutant', 'Intermediaire', 'Expert', 'Professionnel'
                )));
                $match->setEventDate($faker->dateTimeBetween('now', '2 years'));
                $match->setStartHour($faker->dateTimeBetween('now', '2 years'));
                $match->setEndHour($faker->dateTimeBetween('now', '2 years'));
                $match->setName($faker->randomElement(array (
                    'Match de folie', 'Match en plein air', 'Match sur terre battue',
                    'Match pour Débutant', 'Match experimenté', 'Entrainement',
                    'Match féminin', 'Match masculin',
                )));
                $match->setDescription($faker->paragraph());
                $randKey = array_rand($this->cities, 1);
                $match->setAdress($this->cities[$randKey]);
                $match->setOrganizer($this->getReference('admin'));
                $match->addParticipent($this->getReference('admin'));
                $manager->persist($match);
            }
        }
        $manager->flush();
    }
}
