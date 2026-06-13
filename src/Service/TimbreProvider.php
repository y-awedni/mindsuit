<?php

namespace App\Service;

use App\Entity\Timbre;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Single source of truth for the current droit de timbre value. Used both as a
 * Twig global (live invoice calculation) and by controllers (stored per-invoice
 * at creation). Falls back to 0.600 if the config row is missing.
 */
class TimbreProvider
{
    private EntityManagerInterface $em;
    private ?string $cached = null;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getValeur(): string
    {
        if (null === $this->cached) {
            $timbre = $this->em->getRepository(Timbre::class)->findOneBy([]);
            $this->cached = $timbre ? (string) $timbre->getValeur() : '0.600';
        }

        return $this->cached;
    }

    public function getEntity(): ?Timbre
    {
        return $this->em->getRepository(Timbre::class)->findOneBy([]);
    }
}
