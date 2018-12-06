<?php

namespace AppBundle\Entity\Repository;

class BugRepository extends \Doctrine\ORM\EntityRepository
{
    public function searchQuery()
    {
        return $this->_em->getRepository('AppBundle:Bug')->createQueryBuilder('e')->where('e.status=0');
    }
    public function searchTrashQuery()
    {
        return $this->_em->getRepository('AppBundle:Bug')->createQueryBuilder('e')->where('e.status=1');
    }
    public function findOneByIdQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Bug p
            WHERE p.id = :id
            "
        );
        $query->setParameter('id', $id);
        return $query;
    }
    public function findAllQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Bug p
            WHERE p.status = 0
            "
        );
        return $query;
    }
    public function findInProgressQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Bug p
            WHERE p.status = 2
            "
        );
        return $query;
    }
    public function findDoneQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Bug p
            WHERE p.status = 3
            "
        );
        return $query;
    }

    public function findDelayQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Bug p
            WHERE p.status = 4
            "
        );
        return $query;
    }

    public function findTrashQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Bug p
            WHERE p.status = 1
            "
        );
        return $query;
    }

    public function findOnlyOwnByIdQuery($id, $userId)
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Bug p
            WHERE p.id = :id 
            AND p.user = :userId
            "
        );
        $query->setParameter('id', $id);
        $query->setParameter('userId', $userId);
        return $query;
    }

    public function findOnlyOwnQuery( $userId)
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Bug p
            WHERE p.user = :userId
            "
        );
        $query->setParameter('userId', $userId);
        return $query;
    }

    public function deleteQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            DELETE 
            FROM AppBundle:Bug p
            WHERE p.id = :id
            
            "
        );
        $query->setParameter('id', $id);

        return $query;
    }
    public function safeDeleteQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            Update AppBundle:Bug p
            SET p.status = 1
            WHERE p.id = :id
            
            "
        );
        $query->setParameter('id', $id);

        return $query;
    }

}
