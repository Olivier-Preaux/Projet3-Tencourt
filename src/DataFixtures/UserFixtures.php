<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use App\Service\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
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

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $slugify = new Slugify();
        $genres = ['male', 'female'];
       


        $userReferenceNumber = 0;

        // Create an admin User
        $admin = new User();       
        $admin->setEmail('admin@monsite.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPseudo('Administrator');
        $admin->setSex('Homme');
        $admin->setLevel('Expert');
        $admin->setPhone('06 10 20 30 40');
        $admin->setBirthdate(new DateTime('1990/12/24'));
        $admin->setCity('Bakersfield');
        $admin->setSlug('admin');
        $admin->setDescription('Je suis l\'administrateur de cette application');
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'test'
        ));

        $genre = $faker->randomElement($genres);
        $picture = "https://randomuser.me/api/portraits/";
        $pictureId = $faker->numberBetween(1, 99) . '.jpg';
        $picture = $picture.($genre == 'male' ? 'men/' : 'women/') . $pictureId;
        $admin->setAvatar($picture);

        $manager->persist($admin);
        $this->addReference('admin', $admin);



        //Generate Users
        for ($i = 0; $i < 30; $i++) {
            // Création d’un utilisateur de type “contributeur” (= auteur)
            $contributor = new User();
           
            $genre = $faker->randomElement($genres);
            $picture = "https://randomuser.me/api/portraits/";
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';
            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;
            $contributor->setAvatar($picture);

            $contributor->setSex($genre == 'male' ? 'Homme' : 'Femme');
            $contributor->setPseudo($genre =='male' ? $faker->firstNameMale : $faker->firstNameFemale);
            $contributor->setEmail($faker->email);
            $contributor->setRoles(['ROLE_CONTRIBUTOR']);
            
           
            $contributor->setLevel($faker->randomElement(array(
                'Débutant', 'Intermediaire', 'Expert', 'Professionnel'
            )));
            $contributor->setPhone($faker->phoneNumber);
            $contributor->setBirthdate(new DateTime($faker->date(
                'Y-m-d', //format
                '2005-12-12' //date max - You can replace the date by 'now' to get the actual date
            )));
            $randKey = array_rand($this->cities, 1);
            $contributor->setCity($this->cities[$randKey]);
            $contributor->setPostalcode($faker->postcode);
            $contributor->setAddress($faker->streetAddress);
            $slug = $slugify->generate($contributor->getPseudo() ?? '');
           
            $contributor->setSlug($slug);
            $contributor->setDescription($faker->sentence(20));
            $contributor->setPassword($this->passwordEncoder->encodePassword(
                $contributor,
                'password'
            ));

            

            $manager->persist($contributor);
            $this->addReference('user' . $userReferenceNumber, $contributor);
            $userReferenceNumber++;
        }

        $manager->flush();
    }
}
