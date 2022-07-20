<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $startLatitude = null;

    #[ORM\Column]
    private ?float $startLongitude = null;

    #[ORM\Column]
    private ?int $spots = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $meetingPoint = null;

    #[ORM\ManyToOne(inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $driver = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'trips')]
    private ?Collection $passengers = null;

    public function __construct()
    {
        $this->passengers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartLatitude(): ?float
    {
        return $this->startLatitude;
    }

    public function setStartLatitude(float $startLatitude): self
    {
        $this->startLatitude = $startLatitude;

        return $this;
    }

    public function getStartLongitude(): ?float
    {
        return $this->startLongitude;
    }

    public function setStartLongitude(float $startLongitude): self
    {
        $this->startLongitude = $startLongitude;

        return $this;
    }

    public function getSpots(): ?int
    {
        return $this->spots;
    }

    public function setSpots(int $spots): self
    {
        $this->spots = $spots;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMeetingPoint(): ?string
    {
        return $this->meetingPoint;
    }

    public function setMeetingPoint(string $meetingPoint): self
    {
        $this->meetingPoint = $meetingPoint;

        return $this;
    }

    public function getDriver(): ?User
    {
        return $this->driver;
    }

    public function setDriver(?User $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getPassengers(): Collection
    {
        return $this->passengers;
    }

    public function addPassenger(User $passenger): self
    {
        if (!$this->passengers->contains($passenger)) {
            $this->passengers[] = $passenger;
        }

        return $this;
    }

    public function removePassenger(User $passenger): self
    {
        $this->passengers->removeElement($passenger);

        return $this;
    }
}
