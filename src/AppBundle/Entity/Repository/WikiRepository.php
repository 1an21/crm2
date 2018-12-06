<?php

namespace AppBundle\Entity\Repository;

class WikiRepository extends \Doctrine\ORM\EntityRepository
{
    public function getWikiQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT w
            FROM AppBundle:Wiki w
            "
        );
        return $query;
    }

    public function getWikiAllQuery($project)
    {
        $query = $this->_em->createQuery(
            "
            SELECT w
            FROM AppBundle:Wiki w
            WHERE w.project = :project
            "
        );
        $query->setParameter('project', $project);
        return $query;
    }


}
