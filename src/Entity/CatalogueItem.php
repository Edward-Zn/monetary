<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 */
class CatalogueItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $identificationCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $cost;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentificationCode(): ?string
    {
        return $this->identificationCode;
    }

    public function setIdentificationCode(string $identificationCode): static
    {
        $this->identificationCode = $identificationCode;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(string $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function generateIdentificationCode(): string
    {
        $uuid = Uuid::uuid4();
        $identificationCode = $uuid->toString();

        return $identificationCode;
    }

    public function prepareForPersist()
    {
        $this->identificationCode = $this->generateIdentificationCode();
    }

    /**
     * Convert cost string ("Xp Ys Zd") to pence integer.
     *
     * @param string $costString The cost string in the format "Xp Ys Zd"
     *
     * @return int|null The total cost in pence or null if the input is invalid
     */
    public function parseCost(string $costString): ?int
    {
        $pattern = '/^(?:(\d+)p)?\s*(?:(\d+)s)?\s*(?:(\d+)d)?$/';

        if (preg_match($pattern, $costString, $matches)) {
            $pounds = isset($matches[1]) ? (int)$matches[1] : 0;
            $shillings = isset($matches[2]) ? (int)$matches[2] : 0;
            $pences = isset($matches[3]) ? (int)$matches[3] : 0;

            return ($pounds * 20 * 12) + ($shillings * 12) + $pences;
        }

        return null;
    }
}