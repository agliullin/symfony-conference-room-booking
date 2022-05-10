<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use App\Traits\DatetimeRange;
use App\Validator as BookingAssert;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Booking
 *
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{
    /**
     * use DatetimeRange trait
     */
    use DatetimeRange;

    /**
     * Minimum interval on datetime range in seconds
     */
    const MIN_INTERVAL = 1800;

    /**
     * Booking statuses
     */
    const STATUS_WAITING = 'Wait';
    const STATUS_PROCESSING = 'Process';
    const STATUS_COMPLETED = 'Complete';

    /**
     * Booking unique ID
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
     * Datetime range
     *
     * @var string
     *
     * @BookingAssert\BookingDatetimeRange()
     */
    private $datetimeRange;

    /**
     * Start datetime
     *
     * @var DateTime
     *
     * @ORM\Column(name="start_datetime", type="datetime")
     */
    private $startDatetime;

    /**
     * End datetime
     *
     * @var DateTime
     *
     * @ORM\Column(name="end_datetime", type="datetime")
     */
    private $endDatetime;

    /**
     * User owner: many-to-one relation
     *
     * @var User
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ownerBookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * Room: many-to-one relation
     *
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity=Room::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $room;

    /**
     * Name
     *
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\NotNull
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * Color
     *
     * @var string
     *
     * @Assert\CssColor(
     *     formats = Assert\CssColor::HEX_LONG,
     *     message = "The accent color must be a 6-character hexadecimal color."
     * )
     * @ORM\Column(name="color", type="string", length=7)
     */
    private $color;

    /**
     * Booking member collection: many-to-many relation
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="memberBookings")
     * @ORM\JoinTable(name="booking_member",
     *     joinColumns={@ORM\JoinColumn(name="booking_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="member_id", referencedColumnName="id")}
     * )
     **/
    private $bookingMembers;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        $this->uuid = Uuid::v4();
        $this->bookingMembers = new ArrayCollection();
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
    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    /**
     * Get datetime range
     *
     * @return string
     */
    public function getDatetimeRange(): ?string
    {
        if ($this->datetimeRange) {
            return $this->datetimeRange;
        } elseif ($this->getStartDatetime() && $this->getEndDatetime()) {
            return $this->getStartDatetime()->format('d.m.Y H:i') . ' - ' . $this->getEndDatetime()->format('d.m.Y H:i');
        }
        $datetime = new DateTime();
        return $datetime->format('d.m.Y H:i') . ' - ' . $datetime->modify('+30 min')->format('d.m.Y H:i');
    }

    /**
     * Set datetime range
     *
     * @param string|null $datetimeRange
     * @return Booking
     */
    public function setDatetimeRange(?string $datetimeRange): self
    {
        $this->datetimeRange = $datetimeRange;
        return $this;
    }

    /**
     * Get start datetime
     *
     * @return DateTimeInterface
     */
    public function getStartDatetime(): ?DateTimeInterface
    {
        return $this->startDatetime;
    }

    /**
     * Set start datetime
     *
     * @param DateTimeInterface $startDatetime
     * @return Booking
     */
    public function setStartDatetime(DateTimeInterface $startDatetime): self
    {
        $this->startDatetime = $startDatetime;

        return $this;
    }

    /**
     * Get end datetime
     *
     * @return DateTimeInterface
     */
    public function getEndDatetime(): ?DateTimeInterface
    {
        return $this->endDatetime;
    }

    /**
     * Set end datetime
     *
     * @param DateTimeInterface $endDatetime
     * @return Booking
     */
    public function setEndDatetime(DateTimeInterface $endDatetime): self
    {
        $this->endDatetime = $endDatetime;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * Set owner
     *
     * @param User $owner
     * @return Booking
     */
    public function setOwner(User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Get room
     *
     * @return Room
     */
    public function getRoom(): ?Room
    {
        return $this->room;
    }

    /**
     * Set room
     *
     * @param Room $room
     * @return Booking
     */
    public function setRoom(Room $room): self
    {
        $this->room = $room;
        return $this;
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
     * @return Booking
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return Booking
     */
    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Validate
     *
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     * @return void
     */
    public function validateDatetimeRangeConsistency(ExecutionContextInterface $context)
    {
        $dates = $this->getDatetimeRangeFromFormat($this->getDatetimeRange(), 'd.m.Y H:i');
        if ($dates) {
            $this->setStartDatetime(reset($dates));
            $this->setEndDatetime(end($dates));
            $this->setDatetimeRange($this->getDatetimeRange());
        } else {
            $context
                ->buildViolation('Datetime range is not filled')
                ->atPath('datetime_range')
                ->addViolation();
        }
    }

    /**
     * Validate datetime range min interval
     *
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     * @return void
     */
    public function validateDatetimeRangeMinInterval(ExecutionContextInterface $context)
    {
        if ($this->getEndDatetime() && $this->getStartDatetime()
            && $this->getEndDatetime()->format('U') - $this->getStartDatetime()->format('U') < self::MIN_INTERVAL) {
            $context
                ->buildViolation('Minimum difference: 30 minutes')
                ->atPath('datetime_range')
                ->addViolation();
        }
    }

    /**
     * Get process status
     *
     * @return string
     */
    public function getProcessStatus(): string
    {
        $currentDatetime = new DateTime();
        if ($currentDatetime < $this->getStartDatetime()) {
            return self::STATUS_WAITING;
        } elseif ($currentDatetime < $this->getEndDatetime()) {
            return self::STATUS_PROCESSING;
        }
        return self::STATUS_COMPLETED;
    }

    /**
     * Get process percentage
     *
     * @return float
     */
    public function getProcessPercentage(): float
    {
        $currentDatetime = new DateTime();
        if ($currentDatetime < $this->getStartDatetime()) {
            return 0;
        }
        if ($currentDatetime > $this->getEndDatetime()) {
            return 100;
        }

        $timePassed = $currentDatetime->getTimestamp() - $this->getStartDatetime()->getTimestamp();
        $timeFull = $this->getEndDatetime()->getTimestamp() - $this->getStartDatetime()->getTimestamp();

        return round($timePassed / $timeFull * 100, 1);
    }

    /**
     * Get booking members
     *
     * @return Collection
     */
    public function getBookingMembers(): Collection
    {
        return $this->bookingMembers;
    }

    /**
     * Set booking members
     *
     * @param $bookingMembers
     * @return Booking
     */
    public function setBookingMembers($bookingMembers): self
    {
        $this->bookingMembers = $bookingMembers;

        return $this;
    }

    /**
     * Add booking member
     *
     * @param $bookingMember
     * @return Booking
     */
    public function addBookingMember($bookingMember): self
    {
        if (!$this->bookingMembers->contains($bookingMember)) {
            $this->bookingMembers[] = $bookingMember;
        }

        return $this;
    }

    /**
     * Remove booking member
     *
     * @param $bookingMember
     * @return Booking
     */
    public function removeBookingMember($bookingMember): self
    {
        $this->bookingMembers->removeElement($bookingMember);

        return $this;
    }

}
