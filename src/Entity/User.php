<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Il existe déja un compte avec cet email")
 * @UniqueEntity(fields={"pseudo"}, message="Ce pseudo existe déjà")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private ?string $email;

    /**
     * @var array<string>
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private ?string $pseudo;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private ?string $sex;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $level;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $address;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private ?string $postalcode;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private ?string $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private ?string $phone;

    /**
     * @ORM\Column(type="date")
     */
    private ?DateTime $birthdate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $lastname;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="friends")
     */
    private $friend;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="friend")
     */
    private $friends;

    /**
     * @ORM\OneToMany(targetEntity=TennisMatch::class, mappedBy="organizer", orphanRemoval=true)
     */
    private $tennisMatches;

    /**
     * @ORM\ManyToMany(targetEntity=TennisMatch::class, inversedBy="participent")
     */
    private $participationMatch;

    public function __construct()
    {
        $this->friend = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->tennisMatches = new ArrayCollection();
        $this->participationMatch = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalcode(): ?string
    {
        return $this->postalcode;
    }

    public function setPostalcode(?string $postalcode): self
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBirthdate(): ?DateTime
    {
        return $this->birthdate;
    }

    public function setBirthdate(DateTime $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getAge(): int
    {
        $date = new DateTime();
        $dateInterval = $this->birthdate->diff($date);

        return $dateInterval->y;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFriend(): Collection
    {
        return $this->friend;
    }

    public function addFriend(self $friend): self
    {
        if (!$this->friend->contains($friend)) {
            $this->friend[] = $friend;
        }

        return $this;
    }

    public function removeFriend(self $friend): self
    {
        $this->friend->removeElement($friend);

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    /**
     * @return Collection|TennisMatch[]
     */
    public function getTennisMatches(): Collection
    {
        return $this->tennisMatches;
    }

    public function addTennisMatch(TennisMatch $tennisMatch): self
    {
        if (!$this->tennisMatches->contains($tennisMatch)) {
            $this->tennisMatches[] = $tennisMatch;
            $tennisMatch->setOrganizer($this);
        }

        return $this;
    }

    public function removeTennisMatch(TennisMatch $tennisMatch): self
    {
        if ($this->tennisMatches->removeElement($tennisMatch)) {
            // set the owning side to null (unless already changed)
            if ($tennisMatch->getOrganizer() === $this) {
                $tennisMatch->setOrganizer(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return (string) $this->id;
    }

    /**
     * @return Collection|TennisMatch[]
     */
    public function getParticipationMatch(): Collection
    {
        return $this->participationMatch;
    }

    public function addParticipationMatch(TennisMatch $participationMatch): self
    {
        if (!$this->participationMatch->contains($participationMatch)) {
            $this->participationMatch[] = $participationMatch;
        }

        return $this;
    }

    public function removeParticipationMatch(TennisMatch $participationMatch): self
    {
        $this->participationMatch->removeElement($participationMatch);

        return $this;
    }
}
