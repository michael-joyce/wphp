<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Firm;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * FirmRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FirmRepository extends EntityRepository
{
    /**
     * Return the next firm by ID.
     *
     * @param Firm $firm
     * @return Firm|Null
     */
    public function next(Firm $firm) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.id > :id');
        $qb->setParameter('id', $firm->getId());
        $qb->addOrderBy('e.id', 'ASC');
        $qb->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Return the next firm by ID.
     *
     * @param Firm $firm
     * @return Firm|Null
     */
    public function previous(Firm $firm) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.id < :id');
        $qb->setParameter('id', $firm->getId());
        $qb->addOrderBy('e.id', 'DESC');
        $qb->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Simple search, based on substring matching.
     *
     * @param string $q
     * @return array
     */
    public function searchQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.name like '%$q%'");
        return $qb->getQuery();
    }

    /**
     * MySQL fulltext searching via match/against (which is included in a
     * doctrine extension).
     *
     * @param string $q
     * @return Query
     */
    public function fulltextQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->addSelect("MATCH (e.name) AGAINST(:q BOOLEAN) as score");
        $qb->add('where', "MATCH (e.name) AGAINST(:q BOOLEAN) > 0.5");
        $qb->orderBy('score', 'desc');
        $qb->setParameter('q', $q);
        return $qb->getQuery();
    }

    /**
     * Build a full text, complex search query and return it. Takes all the
     * parameters from the firm search and does smart things with them.
     *
     * @param array $data The search form's data from $form->getData().
     * @return Query
     */
    public function buildSearchQuery($data) {
        $qb = $this->createQueryBuilder('e');
        if(isset($data['name']) && $data['name']) {
            $qb->add('where', "MATCH (e.name) AGAINST(:name BOOLEAN) > 0");
            $qb->setParameter('name', $data['name']);
        }
        if(isset($data['address']) && $data['address']) {
            $qb->add('where', "MATCH (e.streetAddress) AGAINST(:address BOOLEAN) > 0");
            $qb->setParameter('address', $data['address']);
        }
        if (isset($data['city']) && $data['city']) {
            $qb->innerJoin('e.city', 'c');
            $qb->andWhere('MATCH(c.alternatenames, c.name) AGAINST(:cname BOOLEAN) > 0');
            $qb->setParameter('cname', $data['city']);
        }

        if (isset($data['start']) && $data['start']) {
            $m = array();
            if (preg_match('/^\s*[0-9]{4}\s*$/', $data['start'])) {
                $qb->andWhere('YEAR(e.startDate) = :yearb');
                $qb->setParameter('yearb', $data['start']);
            } elseif (preg_match('/^\s*(\*|[0-9]{4})\s*-\s*(\*|[0-9]{4})\s*$/', $data['start'], $m)) {
                $from = ($m[1] === '*' ? -1 : $m[1]);
                $to = ($m[2] === '*' ? 9999 : $m[2]);
                $qb->andWhere(':fromb <= YEAR(e.startDate) AND YEAR(e.startDate) <= :tob');
                $qb->setParameter('fromb', $from);
                $qb->setParameter('tob', $to);
            }
        }

        if (isset($data['end']) && $data['end']) {
            $m = array();
            if (preg_match('/^\s*[0-9]{4}\s*$/', $data['end'])) {
                $qb->andWhere('YEAR(e.endDate) = :yeare');
                $qb->setParameter('yeare', $data['end']);
            } elseif (preg_match('/^\s*(\*|[0-9]{4})\s*-\s*(\*|[0-9]{4})\s*$/', $data['end'], $m)) {
                $from = ($m[1] === '*' ? -1 : $m[1]);
                $to = ($m[2] === '*' ? 9999 : $m[2]);
                $qb->andWhere(':frome <= YEAR(e.endDate) AND YEAR(e.endDate) <= :toe');
                $qb->setParameter('frome', $from);
                $qb->setParameter('toe', $to);
            }
        }
        return $qb->getQuery();
    }

    /**
     * Find and return $limit firms, selected at random.
     *
     * @param int $limit
     * @return Collection
     */
    public function random($limit) {
        $qb = $this->createQueryBuilder('e');
        $qb->orderBy('RAND()');
        $qb->setMaxResults($limit);
        return $qb->getQuery()->execute();
    }
}
