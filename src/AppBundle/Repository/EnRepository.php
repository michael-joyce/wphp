<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * EnRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EnRepository extends EntityRepository {

    /**
     * @param string $q
     *
     * @return \Doctrine\ORM\Query
     */
    public function searchQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere("MATCH (e.author, e.title) AGAINST(:q BOOLEAN) > 0");
        $qb->setParameter('q', $q);
        return $qb->getQuery();
    }

}
