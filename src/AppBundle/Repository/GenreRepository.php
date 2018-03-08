<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Firm;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GenreRepository extends EntityRepository
{
    
    public function typeaheadQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere("e.name LIKE :q");
        $qb->orderBy('e.name');
        $qb->setParameter('q', "{$q}%");
        return $qb->getQuery()->execute();
    }
}
