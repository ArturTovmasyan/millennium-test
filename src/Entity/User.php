<?php

namespace App\Entity;

use App\Model\TimeAwareTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class User
{
    use TimeAwareTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 64)]
    #[Assert\Length(max: 64)]
    #[Assert\NotBlank]
    private ?string $firstName;

    #[ORM\Column(length: 64)]
    #[Assert\Length(max: 64)]
    #[Assert\NotBlank]
    private ?string $secondName;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Date]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $birthday;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserOrder::class, cascade: ['persist'])]
    private Collection $orders;

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->secondName;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSecondName(): string
    {
        return $this->secondName;
    }

    public function setSecondName(string $secondName): void
    {
        $this->secondName = $secondName;
    }

    public function getBirthday(): \DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }
}
