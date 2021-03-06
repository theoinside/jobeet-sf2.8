<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * JobRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class JobRepository extends EntityRepository
{

    /**
     * @param int|null $category_id
     * @param int|null $max
     * @param int|null $offset
     * @return array
     */
    public function getActiveJobs(
        $category_id = null,
        $max = null,
        $offset = null
    ) {
        $qb = $this->createQueryBuilder('j')
            ->where('j.expiresAt > :date')
            ->setParameter(
                'date',
                date('Y-m-d H:i:s', time())
            )
            ->orderBy('j.expiresAt', 'DESC');

        if ($max) {
            $qb->setMaxResults($max);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($category_id) {
            $qb->andWhere('j.category = :category_id')
                ->setParameter('category_id', $category_id);
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @param int|null $category_id
     * @return mixed
     */
    public function countActiveJobs($category_id = null)
    {
        $qb = $this->createQueryBuilder('j')
            ->select('count(j.id)')
            ->where('j.expiresAt > :date')
            ->setParameter('date', date('Y-m-d H:i:s', time()));
        if($category_id)
        {
            $qb->andWhere('j.category = :category_id')
                ->setParameter('category_id', $category_id);
        }
        $query = $qb->getQuery();

        return $query->getSingleScalarResult();
    }
}
