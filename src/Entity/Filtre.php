<?php

namespace App\Entity;

use DateTimeInterface;

class Filtre
{
    /**
     * @var Campus|null
     */
    private $campus;
    /**
     * @var string|null
     */
    private $nom;
    /**
     * @var DateTimeInterface|null
     */
    private $dateDebut;
    /**
     * @var DateTimeInterface|null
     */
    private $dateLimite;
    /**
     * @var boolean|null
     */
    private $sortieOrganisateur;
    /**
     * @var boolean|null
     */
    private $sortieInscrit;
    /**
     * @var boolean|null
     */
    private $sortieNonInscrit;
    /**
     * @var boolean|null
     */
    private $sortiePassee;

    /**
     * @return string|null
     */
    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus|null $campus
     */
    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return string|null
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string|null $nom
     */
    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateDebut(): ?DateTimeInterface
    {
        return $this->dateDebut;
    }

    /**
     * @param DateTimeInterface|null $dateDebut
     */
    public function setDateDebut(?DateTimeInterface $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateLimite(): ?DateTimeInterface
    {
        return $this->dateLimite;
    }

    /**
     * @param DateTimeInterface|null $dateLimite
     */
    public function setDateLimite(?DateTimeInterface $dateLimite): void
    {
        $this->dateLimite = $dateLimite;
    }

    /**
     * @return bool|null
     */
    public function getSortieOrganisateur(): ?bool
    {
        return $this->sortieOrganisateur;
    }

    /**
     * @param bool|null $sortieOrganisateur
     */
    public function setSortieOrganisateur(?bool $sortieOrganisateur): void
    {
        $this->sortieOrganisateur = $sortieOrganisateur;
    }

    /**
     * @return bool|null
     */
    public function getSortieInscrit(): ?bool
    {
        return $this->sortieInscrit;
    }

    /**
     * @param bool|null $sortieInscrit
     */
    public function setSortieInscrit(?bool $sortieInscrit): void
    {
        $this->sortieInscrit = $sortieInscrit;
    }

    /**
     * @return bool|null
     */
    public function getSortieNonInscrit(): ?bool
    {
        return $this->sortieNonInscrit;
    }

    /**
     * @param bool|null $sortieNonInscrit
     */
    public function setSortieNonInscrit(?bool $sortieNonInscrit): void
    {
        $this->sortieNonInscrit = $sortieNonInscrit;
    }

    /**
     * @return bool|null
     */
    public function getSortiePassee(): ?bool
    {
        return $this->sortiePassee;
    }

    /**
     * @param bool|null $sortiePassee
     */
    public function setSortiePassee(?bool $sortiePassee): void
    {
        $this->sortiePassee = $sortiePassee;
    }



}