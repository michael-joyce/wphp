<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Format;
use AppBundle\Entity\Title;
use Doctrine\ORM\EntityRepository;

/**
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FormatRepository extends EntityRepository {

    public function typeaheadQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere("e.name LIKE :q");
        $qb->orderBy('e.name');
        $qb->setParameter('q', "{$q}%");
        return $qb->getQuery()->execute();
    }

    public function countTitles(Format $format) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(title.id)');
        $qb->andWhere('title.format = :format');
        $qb->setParameter('format', $format);
        $qb->from(Title::class, 'title');
        return $qb->getQuery()->getSingleScalarResult();
    }

}
