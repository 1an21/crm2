<?php

namespace AppBundle\Entity\Repository;

class PauseRepository extends \Doctrine\ORM\EntityRepository
{
    public function finishQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            UPDATE AppBundle:Pause p
            SET p.dateFinished = CURRENT_TIME()
            WHERE p.task = :id
            "
        );
        $query->setParameter('id', $id);
        return $query;
    }


}
