<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Service\Generator\Image\ImageFactory;
use App\Service\Generator\Image\User\Avatar;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * User unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Uuid
     *
     * @var Uuid
     *
     * @ORM\GeneratedValue
     * @ORM\Column(type="uuid", unique=true)
     */
    private $uuid;

    /**
     * E-mail
     *
     * @var string
     *
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email
     */
    private $email;

    /**
     * Roles
     *
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * The hashed password
     *
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * Avatar
     *
     * @var string
     *
     * @ORM\Column(type="string", length=512)
     */
    private $avatar;

    /**
     * Hidden value to avatar remove
     *
     * @var integer
     */
    private $avatarRemove;

    /**
     * Owner booking collection: one-to-many relation
     *
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="owner", orphanRemoval=true)
     */
    private $ownerBookings;

    /**
     * Member booking collection: many-to-many relation
     *
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity=Booking::class, mappedBy="bookingMembers")
     */
    private $memberBookings;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->ownerBookings = new ArrayCollection();
        $this->memberBookings = new ArrayCollection();
    }

    /**
     * Get Id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get Uuid
     *
     * @return Uuid
     */
    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    /**
     * Get e-mail
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set e-mail
     *
     * @param string $email
     * @return User
     */
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
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * Get username
     *
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * Get roles
     *
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
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get password
     *
     * @see PasswordAuthenticatedUserInterface
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * Get default avatar
     *
     * @return string
     * @throws Exception
     */
    public function getDefaultAvatar(): string
    {
        $defaultAvatarFilename = $this->getUuid()->__toString() . '.jpg';
        $defaultAvatarPath = $this->getDefaultAvatarDir() . $defaultAvatarFilename;
        while (!file_exists($defaultAvatarPath)) {
            $factory = new ImageFactory();
            $userAvatar = $factory->generate(new Avatar(250, crc32($this->getUuid()->__toString())));
            $userAvatar->save($this->getDefaultAvatarDir(), $defaultAvatarFilename);
        }
        return $defaultAvatarFilename;
    }

    /**
     * Get public avatar path to show in front
     *
     * @return string
     * @throws Exception
     */
    public function getPublicAvatar(): string
    {
        if ($this->getAvatar()) {
            return '/public/' . $this->getAvatarDir() . $this->getAvatar();
        }
        return '/public/' . $this->getDefaultAvatarDir() . $this->getDefaultAvatar();
    }

    /**
     * Get avatar directory
     *
     * @return string
     */
    public function getAvatarDir(): string
    {
        return 'uploads/avatars/';
    }

    /**
     * Get default avatar directory
     *
     * @return string
     */
    public function getDefaultAvatarDir(): string
    {
        return $this->getAvatarDir() . 'default/';
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * Get avatar remove
     *
     * @return int
     */
    public function getAvatarRemove(): ?int
    {
        return $this->avatarRemove;
    }

    /**
     * Set avatar remove
     *
     * @param int $avatarRemove
     * @return User
     */
    public function setAvatarRemove(int $avatarRemove): self
    {
        $this->avatarRemove = $avatarRemove;
        return $this;
    }

    /**
     * Get owner booking collection
     *
     * @return ArrayCollection
     */
    public function getOwnerBookings(): ArrayCollection
    {
        return $this->ownerBookings;
    }

    /**
     * Add owner booking
     *
     * @param Booking $booking
     * @return User
     */
    public function addOwnerBooking(Booking $booking): self
    {
        if (!$this->ownerBookings->contains($booking)) {
            $this->ownerBookings[] = $booking;
            $booking->setOwner($this);
        }

        return $this;
    }

    /**
     * Remove owner booking
     *
     * @param Booking $booking
     * @return User
     */
    public function removeOwnerBooking(Booking $booking): self
    {
        $this->ownerBookings->removeElement($booking);

        return $this;
    }

    /**
     * Get member booking collection
     *
     * @return ArrayCollection
     */
    public function getMemberBookings(): ArrayCollection
    {
        return $this->memberBookings;
    }

    /**
     * Add member booking
     *
     * @param Booking $memberBooking
     * @return User
     */
    public function addMemberBookings(Booking $memberBooking): self
    {
        if (!$this->memberBookings->contains($memberBooking)) {
            $this->memberBookings[] = $memberBooking;
        }

        return $this;
    }

    /**
     * Remove member booking collection
     *
     * @param Booking $memberBooking
     * @return User
     */
    public function removeMemberBookings(Booking $memberBooking): self
    {
        $this->memberBookings->removeElement($memberBooking);

        return $this;
    }
}
