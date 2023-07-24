<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
// use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
// use Ramsey\Uuid\Doctrine\UuidGenerator;

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
     * @ORM\Column(type="string", length=255)
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
        // Your logic to generate the identification code based on the entity properties
        // Example: Generating the identification code from pence, shillings, and pounds
        $identificationCode = $uuid->toString();

        return $identificationCode;
    }

    // Method to be called before persisting the entity to the database
    public function prepareForPersist()
    {
        $this->identificationCode = $this->generateIdentificationCode();
    }
}