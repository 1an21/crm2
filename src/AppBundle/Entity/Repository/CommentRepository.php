<?php

namespace AppBundle\Entity\Repository;

class CommentRepository extends \Doctrine\ORM\EntityRepository
{
    public function getCommentsForBlog($taskId)
    {

        $query = $this->_em->createQuery(
            "
            SELECT c
            FROM AppBundle:Comment c
            WHERE c.task = :task_id
          
            "
        );
        $query->setParameter('task_id', $taskId);
        return $query;
    }


}