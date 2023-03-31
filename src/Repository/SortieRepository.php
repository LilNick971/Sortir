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
        $query->where($query->expr()->not($query->expr()->eq('q.etat', 1)));
        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findSearch(Filtre $filtre, User $user)
    {
        $query = $this
            ->createQueryBuilder('q');

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

        if ($filtre->getSortieOrganisateur()){
            $query = $query
                ->andWhere('q.organisateur = :sOrg')
                ->setParameter('sOrg', $user);
        }

        if ($filtre->getSortieInscrit() && !$filtre->getSortieNonInscrit()){
            $query = $query
//                ->innerJoin('q.users', 'u')
                ->andWhere(':sIns MEMBER OF q.users')
                ->setParameter('sIns', $user);
        }

        if ($filtre->getSortieNonInscrit() && !$filtre->getSortieInscrit()){
            $query = $query
//                ->innerJoin('q.users', 'u')
                ->andWhere(':sNon NOT MEMBER OF q.users')
                ->setParameter('sNon', $user);
        }

        if ($filtre->getSortiePassee()){
            $query =$query
                ->andWhere('q.etat = :sPas')
                ->setParameter('sPas', 5);
        }

        return $query->getQuery()->getResult();

    }

}
