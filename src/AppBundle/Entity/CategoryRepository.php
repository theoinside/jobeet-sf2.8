<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getWithJobs()
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->leftJoin('c.jobs', 'j')
            ->andWhere('j.expiresAt > :date')
            ->setParameter(':date', date('Y-m-d H:i:s', time()))
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int|null $category_id
     * @param int|null $max
     *
     * @return array
     */
    public function getActiveJobs($category_id = null, $max = null)
    {
        $qb = $this->createQueryBuilder('j')
            ->where('j.expiresAt > :date')
            ->setParameter('date', date('Y-m-d H:i:s', time()))
            ->orderBy('j.expiresAt', 'DESC')
        ;

        if ($max) {
            $qb->setMaxResults($max);
        }

        if ($category_id) {
            $qb
                ->andWhere('j.category = :category_id')
                ->setParameter('category_id', $category_id)
            ;
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }
}
