<?php

namespace AppBundle\Entity\Repository;

class PauseRepository extends \Doctrine\ORM\EntityRepository
{
    public function finishQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            UPDATE AppBundle:Pause p
            SET p.dateFinished = CURRENT_TIME(), p.description = 'end'
            WHERE p.task = :id order by p.id desc limit 1
           
            "
        );
        $query->setParameter('id', $id);
        return $query;
    }


}
