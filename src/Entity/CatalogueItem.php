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

    /**
     * Set the cost property from the cost string ("Xp Ys Zd").
     *
     * @param string $costString The cost string in the format "Xp Ys Zd"
     *
     * @return self
     */
    public function setCostFromString(string $costString): self
    {
        $pence = $this->parseCost($costString);
        $this->cost = $pence !== null ? $pence : 0;

        return $this;
    }

    /**
     * Convert pence to the cost string format ("Xp Ys Zd").
     *
     * @param int $pence The cost in pence
     *
     * @return string The cost string in the format "Xp Ys Zd"
     */
    public function formatCost(int $pence): string
    {
        $p = floor($pence / 240);
        $s = floor(($pence % 240) / 12);
        $d = $pence % 12;

        return "$p" . "p " . "$s" . "s " . "$d" . "d";
    }

    /**
     * Add two cost values and return the result in pence.
     *
     * @param int $cost1 The first cost in pence
     * @param int $cost2 The second cost in pence
     *
     * @return int The result of the addition in pence
     */
    public function addCosts(int $cost1, int $cost2): int
    {
        return $cost1 + $cost2;
    }

    /**
     * Subtract one cost value from another and return the result in pence.
     *
     * @param int $cost1 The cost to subtract from (minuend) in pence
     * @param int $cost2 The cost to subtract (subtrahend) in pence
     *
     * @return int The result of the subtraction in pence
     */
    public function subtractCosts(int $cost1, int $cost2): int
    {
        return $cost1 - $cost2;
    }

    /**
     * Multiply a cost value by an integer and return the result in pence.
     *
     * @param int $cost The cost to multiply in pence
     * @param int $multiplier The integer multiplier
     *
     * @return int The result of the multiplication in pence
     */
    public function multiplyCost(int $cost, int $multiplier): int
    {
        return $cost * $multiplier;
    }

    /**
     * Get the formatted cost string ("Xp Ys Zd") from the cost value in pence.
     *
     * @return string The formatted cost string
     */
    // public function getFormattedCost(): string
    // {
    //     $pence = $this->getCost();
    //     $p = floor($pence / 240);
    //     $s = floor(($pence % 240) / 12);
    //     $d = $pence % 12;

    //     return "$p" . "p " . "$s" . "s " . "$d" . "d";
    // }
}