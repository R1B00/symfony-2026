<?php

namespace App\Repository;

use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etudiant>
 */
class EtudiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudiant::class);
    }

    //    /**
    //     * @return Etudiant[] Returns an array of Etudiant objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Etudiant
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findStudentByName($nom)
    {
    $queryBuilder = $this->createQueryBuilder('etudiant')
        ->where('LOWER(etudiant.nom) LIKE LOWER(:nom)')
        ->setParameter(':nom', '%'.$nom.'%');

    $query = $queryBuilder->getQuery();
    $result = $query->getResult();
    return $result;
    // ou directement : return $queryBuilder->getQuery()->getResult();
    }

    // OU avec SQL
    public function findStudentByNameSQL(string $nom): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM etudiant e
            WHERE e.nom like :nom
            ORDER BY e.nom ASC
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['nom' => '%' . $nom . '%']);

        return $resultSet->fetchAllAssociative();
        // ou directement : return $stmt->executeQuery(['nom' => '%' . $nom . '%'])->fetchAllAssociative();
    }

}
