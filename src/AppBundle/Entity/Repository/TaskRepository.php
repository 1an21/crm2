<?php

namespace AppBundle\Entity\Repository;

class TaskRepository extends \Doctrine\ORM\EntityRepository
{

    public function oneTaskDoneQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            SELECT t.id as id, t.title as title, t.status as status, t.image as image, t.description as description, pr.title as project, u.name as user, t.dateFinished as dateF,  t.dateStarted as dateSs, COALESCE(count(com.id),0) as cc,DATE_FORMAT(TIMEDIFF(TIMEDIFF(t.dateFinished, t.dateStarted), IFNULL(sum(TIMEDIFF(p.dateFinished, p.dateStarted)),0)) , '%H h %i m') as diff
            FROM AppBundle:Task t
            left join AppBundle:Comment com with com.task = t.id
            left join AppBundle:Pause p with p.task = t.id
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:Priority pt with pt.id = t.priority
            left join AppBundle:User u with u.id = t.user
            WHERE t.id = :id
            "
        );
        $query->setParameter('id', $id);
        return $query;

    }

    public function getCommentsForBlog($taskId)
    {

        $query = $this->_em->createQuery(
            "
            SELECT com.id, com.comment, u.name as name
            FROM AppBundle:Comment com
            left join AppBundle:User u with u.id = com.user
            WHERE com.task = :task_id
          
            "
        );
        $query->setParameter('task_id', $taskId);
        return $query;
    }

    public function getCommentsCount($taskId)
    {

        $query = $this->_em->createQuery(
            "
            SELECT count(*)
            FROM AppBundle:Comment com
            WHERE com.task = :task_id
          
            "
        );
        $query->setParameter('task_id', $taskId);
        return $query;
    }

    public function searchQuery()
    {
        return $this->_em->getRepository('AppBundle:Task')->createQueryBuilder('e')->select('e.id, e.title as title, e.dateStarted, e.dateFinished, e.description, pr.title as project, u.name as user, COALESCE(count(com.id),0) as cc')->leftJoin('AppBundle:Comment', 'com', 'WITH', 'com.task=e.id')->leftJoin('AppBundle:Project', 'pr', 'WITH', 'pr.id=e.project')->leftJoin('AppBundle:User', 'u', 'WITH', 'u.id=e.user')->where('e.status=0')->groupBy('e.id')
            ->orderBy('e.id', 'DESC');
    }
    public function searchByQuery($project)
    {
        $query = $this->_em->createQuery(
            "
            SELECT t.id as id, t.title as title, pr.title as project, u.name as user, p.dateStarted as dateS, COALESCE(count(com.id),0) as cc, p.dateFinished as dateF, pt.title as priority, t.dateStarted as dateStarted, t.dateFinished as dateFinished
            FROM AppBundle:Task t
            left join AppBundle:Comment com with com.task = t.id
            left join AppBundle:Pause p with p.task = t.id
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:Priority pt with pt.id = t.priority
            left join AppBundle:User u with u.id = t.user
            WHERE t.status = 0 and pr.title = :project
            Group by t.id
            ORDER BY t.dateFinished DESC
            "
        );
        $query->setParameter('project', $project);
        return $query;

    }

    public function searchInQuery($project)
    {
        $query = $this->_em->createQuery(
            "
            SELECT t.id as id, t.title as title, pr.title as project, u.name as user, p.dateStarted as dateS, COALESCE(count(com.id),0) as cc, p.dateFinished as dateF, pt.title as priority, t.dateStarted as dateStarted, t.dateFinished as dateFinished
            FROM AppBundle:Task t
            left join AppBundle:Comment com with com.task = t.id
            left join AppBundle:Pause p with p.task = t.id
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:Priority pt with pt.id = t.priority
            left join AppBundle:User u with u.id = t.user
            WHERE t.status = 2 and pr.title = :project
            Group by t.id
            ORDER BY t.dateFinished DESC
            "
        );
        $query->setParameter('project', $project);
        return $query;

    }

    public function searchDoneQuery($project)
    {
        $query = $this->_em->createQuery(
            "
            SELECT t.id as id, t.title as title, pr.title as project, u.name as user, t.dateFinished as dateF, COALESCE(count(com.id),0) as cc,DATE_FORMAT(TIMEDIFF(TIMEDIFF(t.dateFinished, t.dateStarted), IFNULL(sum(TIMEDIFF(p.dateFinished, p.dateStarted)),0)) , '%H h %i m') as diff
            FROM AppBundle:Task t
            left join AppBundle:Comment com with com.task = t.id
            left join AppBundle:Pause p with p.task = t.id
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:Priority pt with pt.id = t.priority
            left join AppBundle:User u with u.id = t.user
            WHERE t.status = 3 and pr.title = :project
            Group by t.id
            ORDER BY t.dateFinished DESC
            "
        );
        $query->setParameter('project', $project);
        return $query;

    }

    public function searchDelayQuery($project)
    {
        $query = $this->_em->createQuery(
            "
            SELECT t.id as id, t.title as title, pr.title as project, u.name as user, p.dateStarted as dateS, COALESCE(count(com.id),0) as cc, p.dateFinished as dateF, pt.title as priority, t.dateStarted as dateStarted, t.dateFinished as dateFinished
            FROM AppBundle:Task t
            left join AppBundle:Comment com with com.task = t.id
            left join AppBundle:Pause p with p.task = t.id
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:Priority pt with pt.id = t.priority
            left join AppBundle:User u with u.id = t.user
            WHERE t.status = 4 and pr.title = :project
            Group by t.id
            ORDER BY t.dateFinished DESC
            "
        );
        $query->setParameter('project', $project);
        return $query;

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
            SELECT t.id as id, t.title as title, pr.title as project, u.name as user, p.description as maximus, max(p.dateStarted) as dateS, p.description as dscr, COALESCE(count(com.id),0) as cc, max(p.dateFinished) as dateF, pt.title as priority, t.dateStarted as dateStarted, t.dateFinished as dateFinished
            FROM AppBundle:Task t
            left join AppBundle:Comment com with com.task = t.id
            left join AppBundle:Pause p with (p.task = t.id and p.id = (select max(pp.id) from AppBundle:Pause pp))
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:Priority pt with pt.id = t.priority
            left join AppBundle:User u with u.id = t.user
            WHERE t.status = 2
            Group by t.id
            ORDER BY t.dateFinished DESC
            "
        );
        return $query;
    }
    public function findDoneQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT t.id as id, t.title as title, pr.title as project, u.name as user, t.dateFinished as dateF, COALESCE(count(com.id),0) as cc,DATE_FORMAT(TIMEDIFF(TIMEDIFF(t.dateFinished, t.dateStarted), IFNULL(sum(TIMEDIFF(p.dateFinished, p.dateStarted)),0)) , '%H h %i m') as diff
            FROM AppBundle:Task t
            left join AppBundle:Comment com with com.task = t.id
            left join AppBundle:Pause p with p.task = t.id
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:User u with u.id = t.user
            WHERE t.status = 3
            Group by t.id
            ORDER BY t.dateFinished DESC
            "
        );
        return $query;
    }

    public function findDelayQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT t.id as id, t.title as title, pr.title as project, u.name as user, t.dateFinished as dateF, COALESCE(count(com.id),0) as cc,DATE_FORMAT(TIMEDIFF(TIMEDIFF(t.dateFinished, t.dateStarted), IFNULL(TIMEDIFF(p.dateFinished, p.dateStarted),0)) , '%H h %i m') as diff
            FROM AppBundle:Task t
            left join AppBundle:Comment com with com.task = t.id
            left join AppBundle:Pause p with p.task = t.id
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:User u with u.id = t.user
            WHERE t.status = 4
            Group by t.id
            ORDER BY t.dateFinished DESC
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
            SELECT u.name as names, SUBSTRING(SEC_TO_TIME(SUM(TIME_TO_SEC(p.dateFinished) - TIME_TO_SEC(p.dateStarted))),1,5) as times , date_format(p.dateFinished, '%M %d') as finished
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
            SELECT u.name as names, pr.title as title, DATE_FORMAT(TIMEDIFF(TIMEDIFF(p.dateFinished, p.dateStarted), IFNULL(sum(TIMEDIFF(pa.dateFinished, pa.dateStarted)),0)) , '%H h %i m') as times, date_format(p.dateFinished, '%M %d') as finished
            FROM AppBundle:Task p
            JOIN AppBundle:Project pr WITH pr.id=p.project
            JOIN AppBundle:User u WITH u.id=p.user
            left join AppBundle:Pause pa with pa.task = p.id
            where p.dateStarted is not null and p.status = '3'
            GROUP BY p.user, p.project
            
            "
        );

        return $query;
    }

    public function getTimeByEmployeeByDailyQuery()
    {
        $query = $this->_em->createQuery(
            "
                SELECT u.name as names, pr.title as title, DATE_FORMAT(TIMEDIFF(TIMEDIFF(p.dateFinished, p.dateStarted), IFNULL(sum(TIMEDIFF(pa.dateFinished, pa.dateStarted)),0)) , '%H h %i m') as times,  date_format(p.dateFinished, '%M %d') as finished
                FROM AppBundle:Task p
                JOIN AppBundle:Project pr WITH pr.id=p.project
                left join AppBundle:Pause pa with pa.task = p.id
                JOIN AppBundle:User u WITH u.id=p.user
                Where p.dateFinished >= CURRENT_DATE() and (p.dateStarted is not null) and p.status = '3'
                GROUP BY p.user, p.project
            
            "
        );
        return $query;
    }

        public function getTimeByEmployeeByWeeklyQuery()
    {
        $query = $this->_em->createQuery(
            "
            SELECT u.name as names, pr.title as title, DATE_FORMAT(TIMEDIFF(TIMEDIFF(p.dateFinished, p.dateStarted), IFNULL(sum(TIMEDIFF(pa.dateFinished, pa.dateStarted)),0)) , '%H h %i m') as times, date_format(p.dateFinished, '%M %d') as finished
            FROM AppBundle:Task p
            JOIN AppBundle:Project pr WITH pr.id=p.project
            JOIN AppBundle:User u WITH u.id=p.user
            left join AppBundle:Pause pa with pa.task = p.id
            Where YEARWEEK(p.dateFinished,0) = YEARWEEK(CURRENT_TIMESTAMP(),0) and ( p.dateStarted is not null) and p.status = '3'
            GROUP BY p.user, p.project
            
            "
        );


        return $query;
    }

    public function getTimeByDateAndEmployeeQuery($project)
    {
        $query = $this->_em->createQuery(
            "
            SELECT u.name as names, pr.title as title, SUBSTRING(SEC_TO_TIME(SUM(UNIX_TIMESTAMP(p.dateFinished) - UNIX_TIMESTAMP(p.dateStarted))),1,5) as times, date_format(p.dateFinished, '%M %d') as finished
            FROM AppBundle:Task p
            JOIN AppBundle:Project pr WITH pr.id=p.project
            JOIN AppBundle:User u WITH u.id=p.user
            Where p.dateStarted is not null and pr.title = :project and p.status = '3'
            GROUP BY p.user
            
            "
        );
        $query->setParameter('project', $project);


        return $query;
    }

    public function findByEmployeeQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            SELECT t.id as id, t.title as title, pr.title as project, u.name as user, p.dateStarted as dateS, COALESCE(count(com.id),0) as cc, p.dateFinished as dateF, pt.title as priority, t.dateStarted as dateStarted, t.dateFinished as dateFinished
            FROM AppBundle:Task t
            left join AppBundle:Comment com with com.task = t.id
            left join AppBundle:Pause p with p.task = t.id
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:Priority pt with pt.id = t.priority
            left join AppBundle:User u with u.id = t.user
            WHERE t.status = 0 and u.id = :id
            Group by t.id
            ORDER BY t.dateFinished DESC
            "
        );
        $query->setParameter('id', $id);
        return $query;
    }


    public function findByEmployeeInQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            SELECT t.id as id, t.title as title, pr.title as project, u.name as user, p.dateStarted as dateS, COALESCE(count(com.id),0) as cc, p.dateFinished as dateF, pt.title as priority, t.dateStarted as dateStarted, t.dateFinished as dateFinished
            FROM AppBundle:Task t
            left join AppBundle:Comment com with com.task = t.id
            left join AppBundle:Pause p with p.task = t.id
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:Priority pt with pt.id = t.priority
            left join AppBundle:User u with u.id = t.user
            WHERE t.status = 2 and u.id = :id
            Group by t.id
            ORDER BY t.dateFinished DESC
            "
        );
        $query->setParameter('id', $id);
        return $query;
    }

    public function findByEmployeeDoneQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            SELECT t.id as id, t.title as title, pr.title as project, u.name as user, t.dateFinished as dateF, COALESCE(count(com.id),0) as cc,DATE_FORMAT(TIMEDIFF(TIMEDIFF(t.dateFinished, t.dateStarted), IFNULL(sum(TIMEDIFF(p.dateFinished, p.dateStarted)),0)) , '%H h %i m') as diff
            FROM AppBundle:Task t
            left join AppBundle:Comment com with com.task = t.id
            left join AppBundle:Pause p with p.task = t.id
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:User u with u.id = t.user
            WHERE t.status = 3 and u.id = :id
            Group by t.id
            ORDER BY t.dateFinished DESC
            "
        );
        $query->setParameter('id', $id);
        return $query;
    }

    public function findByEmployeeDelayQuery($id)
    {
        $query = $this->_em->createQuery(
            "
            SELECT t.id as id, t.title as title, pr.title as project, u.name as user, t.dateFinished as dateF, COALESCE(count(com.id),0) as cc,DATE_FORMAT(TIMEDIFF(TIMEDIFF(t.dateFinished, t.dateStarted), IFNULL(TIMEDIFF(p.dateFinished, p.dateStarted),0)) , '%H h %i m') as diff
            FROM AppBundle:Task t
            left join AppBundle:Comment com with com.task = t.id
            left join AppBundle:Pause p with p.task = t.id
            left join AppBundle:Project pr with pr.id = t.project
            left join AppBundle:User u with u.id = t.user
            WHERE t.status = 4 and u.id = :id
            Group by t.id
            ORDER BY t.dateFinished DESC
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

    public function getEmailWhoCreatedQuery($who)
    {
        $query = $this->_em->createQuery(
            "
            SELECT distinct u.email
            FROM AppBundle:User u
            Left Join AppBundle:Task p WITH p.whoCreate = u.id
            where p.whoCreate = :who
            "
        );
        $query->setParameter('who', $who);
        return $query;
    }
}
