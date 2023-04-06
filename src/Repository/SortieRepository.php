<?php

namespace App\Repository;

use App\Entity\Filtre;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function listeSortieAffichage() {
        $query = $this->createQueryBuilder("q");
        $query->where('q.etat != 1');
        return $query->getQuery()->getResult();
    }
    public function findSearch(Filtre $filtre, User $user)
    {
        $listeEtat = [2,3,4];
        $etatnecessaire = true;
        $query = $this
            ->createQueryBuilder('q');

        if ($filtre->getSortieInscrit()){
            $query = $query
                ->orWhere(':sIns MEMBER OF q.users')
                ->setParameter('sIns', $user);
            $etatnecessaire = false;
        }

        if ($filtre->getSortieNonInscrit()){
            $query = $query
                ->orWhere(':sNon NOT MEMBER OF q.users')
                ->setParameter('sNon', $user);
            $etatnecessaire = false;
        }

        if ($filtre->getSortiePassee()){
            $query = $query
                ->orWhere('q.etat = :sPas')
                ->setParameter('sPas', 5);
            $etatnecessaire = false;
        }

        if ($filtre->getSortieOrganisateur()){
            $query = $query
                ->orWhere('q.organisateur = :sOrg')
                ->setParameter('sOrg', $user);
            $etatnecessaire = false;
        }

        if($etatnecessaire){
            $query = $query
            ->andWhere('q.etat IN (:etatsValides)')
                ->setParameter('etatsValides', $listeEtat);
        }

        if ($filtre->getCampus()){
            $query = $query
                ->andWhere('q.siteOrganisateur = :campus')
                ->setParameter('campus', $filtre->getCampus());
        }

        if ($filtre->getNom()){
            $query = $query
                ->andWhere('q.nom LIKE :nom')
                ->setParameter('nom', "%{$filtre->getNom()}%");
        }

        if ($filtre->getDateDebut()){
            $query = $query
                ->andWhere('q.dateHeureDebut >= :dDeb')
                ->setParameter('dDeb', $filtre->getDateDebut());
        }

        if ($filtre->getDateLimite()){
            $query = $query
                ->andWhere('q.dateLimiteInscription <= :dLim')
                ->setParameter('dLim', $filtre->getDateLimite());
        }

        return $query->getQuery()->getResult();

    }

}
