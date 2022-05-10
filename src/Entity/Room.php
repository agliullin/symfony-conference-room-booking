<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Room
 *
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 */
class Room
{
    /**
     * Room unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

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
     * Room booking collection: one-to-many relation
     *
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="room", orphanRemoval=true)
     */
    private $bookings;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->bookings = new ArrayCollection();
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
     * @return Room
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get Uuid
     *
     * @return Uuid
     */
    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    /**
     * Get owner booking collection
     *
     * @return ArrayCollection
     */
    public function getBookings(): ArrayCollection
    {
        return $this->bookings;
    }

    /**
     * Add room booking
     *
     * @param Booking $booking
     * @return Room
     */
    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setRoom($this);
        }
        return $this;
    }
}
