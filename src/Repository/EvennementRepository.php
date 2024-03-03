<?php

namespace App\Repository;

use App\Entity\Evennement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EvennementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evennement::class);
    }

    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.nom LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }

    public function findAllSortedBy(string $sortBy): array
    {
        $orderBy = null;
        switch ($sortBy) {
            case 'nb_participant':
                $orderBy = 'e.nbParticipant';
                break;
            case 'name':
                $orderBy = 'e.nom';
                break;
            case 'startDate':
                $orderBy = 'e.date_debut';
                break;
            default:
                $orderBy = 'e.date_debut'; // Default sorting
        }

        return $this->createQueryBuilder('e')
            ->orderBy($orderBy, 'ASC')
            ->getQuery()
            ->getResult();
    }
}
