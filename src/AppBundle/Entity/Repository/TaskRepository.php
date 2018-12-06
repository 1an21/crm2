<?php

namespace AppBundle\Entity\Repository;

class TaskRepository extends \Doctrine\ORM\EntityRepository
{
    public function searchQuery()
    {
        return $this->_em->getRepository('AppBundle:Task')->createQueryBuilder('e')->where('e.status=0')
            ->orderBy('e.id', 'DESC');
    }
    public function searchByQuery($project)
    {
        return $this->_em->getRepository('AppBundle:Task')->createQueryBuilder('e')
            ->join('AppBundle:Project', 'pr', 'WITH', 'pr.id=e.project')
            ->where('pr.title= :project')
            ->andWhere('e.status=0')
            ->orderBy('e.dateFinished', 'DESC')
            ->setParameter('project', $project);

    }

    public function searchInQuery($project)
    {
        return $this->_em->getRepository('AppBundle:Task')->createQueryBuilder('e')
            ->join('AppBundle:Project', 'pr', 'WITH', 'pr.id=e.project')
            ->where('pr.title= :project')
            ->andWhere('e.status=2')
            ->orderBy('e.dateFinished', 'DESC')
            ->setParameter('project', $project);

    }

    public function searchDoneQuery($project)
    {
        return $this->_em->getRepository('AppBundle:Task')->createQueryBuilder('e')
            ->join('AppBundle:Project', 'pr', 'WITH', 'pr.id=e.project')
            ->where('pr.title= :project')
            ->andWhere('e.status=3')
            ->orderBy('e.dateFinished', 'DESC')
            ->setParameter('project', $project);

    }

    public function searchDelayQuery($project)
    {
        return $this->_em->getRepository('AppBundle:Task')->createQueryBuilder('e')
            ->join('AppBundle:Project', 'pr', 'WITH', 'pr.id=e.project')
            ->where('pr.title= :project')
            ->andWhere('e.status=4')
            ->orderBy('e.dateFinished', 'DESC')
            ->setParameter('project', $project);

    }

    public function searchTrashQuery()
    {
        return $this->_em->getRepository('AppBundle:Task')->createQueryBuilder('e')->where('e.status=1');
    }
    public function findOneByIdQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Task p
            WHERE p.id = :id
            ORDER BY p.dateFinished DESC
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
            FROM AppBundle:Task p
            WHERE p.status = 0
            ORDER BY p.dateFinished DESC
            "
        );
        return $query;
    }
    public function findInProgressQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT t.id as id, t.title as title, pr.title as project, u.name as user, p.dateStarted as dateS, p.dateFinished as dateF, pt.title as priority, t.dateStarted as dateStarted, t.dateFinished as dateFinished
            FROM AppBundle:Task t
            left join AppBundle:Pause p with p.task = t.id
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:Priority pt with pt.id = t.priority
            left join AppBundle:User u with u.id = t.user
            WHERE t.status = 2
            ORDER BY t.dateFinished DESC
            "
        );
        return $query;
    }
    public function findDoneQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT t.id as id, t.title as title, pr.title as project, u.name as user, DATE_FORMAT(TIMEDIFF(TIMEDIFF(t.dateFinished, t.dateStarted), IFNULL(TIMEDIFF(p.dateFinished, p.dateStarted),0)) , '%H h %i m') as diff
            FROM AppBundle:Task t
            left join AppBundle:Pause p with p.task = t.id
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:User u with u.id = t.user
            WHERE t.status = 3
            ORDER BY t.dateFinished DESC
            "
        );
        return $query;
    }

    public function findDelayQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Task p
            WHERE p.status = 4
            ORDER BY p.dateFinished DESC
            "
        );
        return $query;
    }

    public function findTrashQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Task p
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
            FROM AppBundle:Task p
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
            FROM AppBundle:Task p
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
            FROM AppBundle:Task p
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
            Update AppBundle:Task p
            SET p.status = 1
            WHERE p.id = :id
            
            "
        );
        $query->setParameter('id', $id);

        return $query;
    }

    public function getTimeQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT SUBSTRING(SEC_TO_TIME(SUM(TIME_TO_SEC(p.dateFinished) - TIME_TO_SEC(p.dateStarted))),1,5)
            FROM AppBundle:Task p
            "
        );

        return $query;
    }

    public function getTimeByQuery($project)
    {
        $query = $this->_em->createQuery(
            "
            SELECT SUBSTRING(SEC_TO_TIME(SUM(TIME_TO_SEC(p.dateFinished) - TIME_TO_SEC(p.dateStarted))),1,5)
            FROM AppBundle:Task p
            JOIN AppBundle:Project pr WITH pr.id=p.project
            WHERE pr.title = :project
            
            "
        );
        $query->setParameter('project', $project);
        return $query;
    }
    public function getTimeAllQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT pr.title as title, SUBSTRING(SEC_TO_TIME(SUM(TIME_TO_SEC(p.dateFinished) - TIME_TO_SEC(p.dateStarted))),1,5) as times
            FROM AppBundle:Task p
            JOIN AppBundle:Project pr WITH pr.id=p.project
            GROUP by pr.title
            "
        );

        return $query;
    }

    public function getTimeByEmployeeQuery($project)
    {
        $query = $this->_em->createQuery(
            "
            SELECT u.name as names, SUBSTRING(SEC_TO_TIME(SUM(TIME_TO_SEC(p.dateFinished) - TIME_TO_SEC(p.dateStarted))),1,5) as times
            FROM AppBundle:Task p
            JOIN AppBundle:Project pr WITH pr.id=p.project
            JOIN AppBundle:User u WITH u.id=p.user
            WHERE pr.title = :project 
            GROUP BY p.user
            
            "
        );
        $query->setParameter('project', $project);

        return $query;
    }

    public function getTimeByEmployeeAllQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT u.name as names, pr.title as title, SUBSTRING(SEC_TO_TIME(SUM(TIME_TO_SEC(p.dateFinished) - TIME_TO_SEC(p.dateStarted))),1,5) as times
            FROM AppBundle:Task p
            JOIN AppBundle:Project pr WITH pr.id=p.project
            JOIN AppBundle:User u WITH u.id=p.user
            where p.dateStarted is not null
            GROUP BY p.user, p.project
            
            "
        );

        return $query;
    }

    public function getTimeByEmployeeByDailyQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT u.name as names, pr.title as title, SUBSTRING(SEC_TO_TIME(SUM(TIME_TO_SEC(p.dateFinished) - TIME_TO_SEC(p.dateStarted))),1,5) as times
            FROM AppBundle:Task p
            JOIN AppBundle:Project pr WITH pr.id=p.project
            JOIN AppBundle:User u WITH u.id=p.user
            Where p.dateFinished >= CURRENT_DATE() and (p.dateStarted is not null)
            GROUP BY p.user, p.project
            
            "
        );
        return $query;
    }

        public function getTimeByEmployeeByWeeklyQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT u.name as names, pr.title as title, SUBSTRING(SEC_TO_TIME(SUM(TIME_TO_SEC(p.dateFinished) - TIME_TO_SEC(p.dateStarted))),1,5) as times
            FROM AppBundle:Task p
            JOIN AppBundle:Project pr WITH pr.id=p.project
            JOIN AppBundle:User u WITH u.id=p.user
            Where YEARWEEK(p.dateFinished,0) = YEARWEEK(CURRENT_TIMESTAMP(),0) and ( p.dateStarted is not null)
            GROUP BY p.user, p.project
            
            "
        );


        return $query;
    }

    public function findByEmployeeQuery($id)
    {
        return $this->_em->getRepository('AppBundle:Task')->createQueryBuilder('e')
            ->where('e.user= :id')
            ->andWhere('e.status=0')
            ->orderBy('e.dateFinished', 'DESC')
            ->setParameter('id', $id);

    }


    public function findByEmployeeInQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Task p
            WHERE p.user = :id AND p.status=2
            ORDER BY p.dateFinished DESC
            "
        );
        $query->setParameter('id', $id);
        return $query;
    }

    public function findByEmployeeDoneQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Task p
            WHERE p.user = :id AND p.status=3
            ORDER BY p.dateFinished DESC
            "
        );
        $query->setParameter('id', $id);
        return $query;
    }

    public function findByEmployeeDelayQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            SELECT p
            FROM AppBundle:Task p
            WHERE p.user = :id AND p.status=4
            ORDER BY p.dateFinished DESC
            "
        );
        $query->setParameter('id', $id);
        return $query;
    }

    public function pauseTimeQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            SELECT TIMEDIFF(TIMEDIFF(t.dateFinished, t.dateStarted), TIMEDIFF(p.dateFinished, p.dateStarted))
            FROM AppBundle:Pause p
            LEFT JOIN AppBundle:Task t WITH t.id=p.task
            WHERE p.task = :id
            "
        );
        $query->setParameter('id', $id);
        return $query;
    }
}
