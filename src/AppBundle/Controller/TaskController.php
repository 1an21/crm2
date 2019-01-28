<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Task;
use AppBundle\Entity\Repository\TaskRepository;
use AppBundle\Form\Type\TaskType;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Form\Type\CommentType;
/**
 * Class TaskController
 * @package AppBundle\Controller
 *
 * @RouteResource("Task")
 */
class TaskController extends FOSRestController implements ClassResourceInterface
{

    /**
     * Gets a collection of Tasks
     *
     * @return array
     *
     * @ApiDoc(
     *     section="Task",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     *
     * @Route("/", name="all")
     * @Method("GET")
     */

    public function allGetAction(Request $request)
    {
        $projects= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project')->findAll();
        $total=$this->getTaskRepository()->getTimeQuery()->getOneOrNullResult();
        $queryBuilder = $this->getTaskRepository()->searchQuery();
        if ($request->query->getAlnum('filter')) {
            $queryBuilder
                ->where('e.title LIKE :name')
                ->orwhere('e.description LIKE :name')
                ->setParameter('name', '%' . $request->query->getAlnum('filter') . '%');
        }
        $query = $queryBuilder->getQuery();
        $paginator  = $this->get('knp_paginator');
        $tasks = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 50)
        );
        $intasks = $this->getTaskRepository()->findInProgressQuery()->getResult();
        $donetasks = $this->getTaskRepository()->findDoneQuery()->getResult();
        $delaytasks = $this->getTaskRepository()->findDelayQuery()->getResult();

        return $this->render('Task/all.html.twig', [
            'tasks' => $tasks, 'inprogress'=>$intasks, 'donetasks'=>$donetasks, 'delaytasks'=>$delaytasks, 'projects'=>$projects, 'total'=>$total]);
    }

    /**
     * Gets a collection of Tasks
     *
     * @return array
     *
     * @ApiDoc(
     *     section="Task",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     *
     * @Route("/q/{project}", name="allby")
     * @Method("GET")
     */

    public function allGetByProjectAction(Request $request, $project)
    {
        $projects= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project')->findAll();
        $total=$this->getTaskRepository()->getTimeByQuery($project)->getOneOrNullResult();
        $employeeTotal=$this->getTaskRepository()->getTimeByEmployeeQuery($project)->getResult();

        $tasks = $this->getTaskRepository()->searchByQuery($project)->getResult();
        $intasks = $this->getTaskRepository()->searchInQuery($project)->getResult();
        $donetasks = $this->getTaskRepository()->searchDoneQuery($project)->getResult();
        $delaytasks = $this->getTaskRepository()->searchDelayQuery($project)->getResult();

        $employeeDate=$this->getTaskRepository()->getTimeByDateAndEmployeeQuery($project)->getResult();
        return $this->render('Task/allby.html.twig', [
            'tasks' => $tasks, 'inprogress'=>$intasks,  'donetasks'=>$donetasks, 'delaytasks'=>$delaytasks, 'projects'=>$projects, 'total'=>$total,
        'employee'=>$employeeTotal, 'employeeDate'=> $employeeDate]);
    }

    /**
     * Gets a collection of Tasks
     *
     * @return array
     *
     * @ApiDoc(
     *     section="Task",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     *
     * @Route("/trash", name="alltrash")
     * @Method("GET")
     */

    public function allTrashAction(Request $request)
    {

        $queryBuilder = $this->getTaskRepository()->searchTrashQuery();
        if ($request->query->getAlnum('filter')) {
            $queryBuilder
                ->where('e.title LIKE :name')
                ->orwhere('e.description LIKE :name')
                ->setParameter('name', '%' . $request->query->getAlnum('filter') . '%');
        }
        $query = $queryBuilder->getQuery();
        $paginator  = $this->get('knp_paginator');
        $tasks = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );
        return $this->render('Task/alltrash.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Gets tasks by employee and project
     *
     * @return array
     *
     * @ApiDoc(
     *     section="Task",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     *
     * @Route("/daily", name="daily")
     * @Method("GET")
     */

    public function taskByEmployeeAndProjectsDailyAction(Request $request)
    {
        $projects= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project')->findAll();
        $employeeTotal=$this->getTaskRepository()->getTimeByEmployeeByDailyQuery()->getResult();
        return $this->render('Task/daily.html.twig', [
          'employees'=>$employeeTotal, 'projects'=>$projects
        ]);
    }

    /**
     * Gets tasks by employee and project
     *
     * @return array
     *
     * @ApiDoc(
     *     section="Task",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     *
     * @Route("/weekly", name="weekly")
     * @Method("GET")
     */

    public function taskByEmployeeAndProjectsWeeklyAction(Request $request)
    {
        $projects= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project')->findAll();
        $employeeTotal=$this->getTaskRepository()->getTimeByEmployeeByWeeklyQuery()->getResult();
        return $this->render('Task/weekly.html.twig', [
            'employees'=>$employeeTotal, 'projects'=>$projects
        ]);
    }

    /**
     * Gets tasks by employee and project
     *
     * @return array
     *
     * @ApiDoc(
     *     section="Task",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     *
     * @Route("/all-schema", name="all-schema")
     * @Method("GET")
     */

    public function taskByEmployeeAndProjectsAction(Request $request)
    {
        $projects= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project')->findAll();
        $employeeTotal=$this->getTaskRepository()->getTimeByEmployeeAllQuery()->getResult();
        return $this->render('Task/all-schema.html.twig', [
            'employees'=>$employeeTotal, 'projects'=>$projects
        ]);
    }

    /**
     * Add a new task
     * @param Request $request
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     section="Task",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         201 = "Returned when a new task has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/creates", name="create")
     * @Method({"POST", "GET"})
     */
    public function postAction(Request $request)
    {
        $projects= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project')->findAll();
        $form = $this->createForm(TaskType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $task = $form->getData();
            if($task->getImage()!=null) {
                $file = $task->getImage();
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                $file->move(
                    $this->getParameter('image_directory'),
                    $fileName
                );

                $task->setImage($fileName);
            }
            $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
            $userCreated = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find( $userId);
            $task->setWhoCreate($userCreated);
            $em->persist($task);
            $em->flush();
            return $this->redirectToRoute('all');
        }
        return $this->render('Task/create.html.twig', [
            'form' => $form->createView(), 'projects'=>$projects
        ]);
    }

    /**
     * get single task
     *
     * @ApiDoc(
     *     section="Task",
     *     input="AppBundle\Form\Type\TaskType",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         204 = "Returned when an existing task has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/view/{id}", name="view")
     * @Method({"GET", "POST"})
     * @return RedirectResponse
     */
    public function getAction(Task $task, Request $request)
    {
        $projects= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project')->findAll();
  	    $taskId = $task->getId();
        $oneTask = $this->getTaskRepository()->oneTaskDoneQuery($taskId)->getOneOrNullResult();
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);
        $who = $task->getWhoCreate();
        $userMail = $this->getTaskRepository()->getEmailWhoCreatedQuery($who)->getOneOrNullResult();
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $comment = $form->getData();

            $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
            $userCreated = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find( $userId);

            $comment->setUser($userCreated);
            $comment->setCreated(new \DateTime());
            $comment->setUpdated(new \DateTime());
            $comment->setTask($task);
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('view', array('id' => $taskId));

            $message = \Swift_Message::newInstance()
                ->setSubject('New comment in Admin Qualiteam')
                ->setFrom('poshchada@gmail.com')
                ->setTo($userMail['email'])
                ->setBody($comment->getComment());
            $this->get('mailer')->send($message);
        }



        $comments = $this->getTaskRepository()->getCommentsForBlog($task->getId())->getResult();
        return $this->render('Task/view.html.twig', [
            'task' => $oneTask, 'who' => $who, 'comments'=>$comments, 'form' => $form->createView(), 'projects'=>$projects
        ]);

    }

    /**
     * Start task
     * @param Request $request
     * @param Task $task
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     section="Task",
     *     input="AppBundle\Form\Type\TaskType",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         204 = "Returned when an existing task has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("edit/{id}", name="edit")
     * @Method({"POST","GET"})
     * @return RedirectResponse
     */
    public function patchAction(Request $request, Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $task->setDateStarted(new \DateTime('now'));
        $task->setStatus('2');
        $em->flush();

        return $this->redirectToRoute('all');
    }

    /**
     * Start task
     * @param Request $request
     * @param Task $task
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     section="Task",
     *     input="AppBundle\Form\Type\TaskType",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         204 = "Returned when an existing task has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/edits/{id}", name="edit-view")
     * @Method({"POST","GET"})
     * @return RedirectResponse
     */
    public function editAction(Request $request, Task $task)
    {
        $projects= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project')->findAll();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            // for now
            return $this->redirectToRoute('all'
            );
        }
        return $this->render('Task/edit-view.html.twig', [
            'form' => $form->createView(), 'projects'=>$projects
        ]);
    }

    /**
     * Finish task
     * @param Request $request
     * @param Task $task
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     section="Task",
     *     input="AppBundle\Form\Type\TaskType",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         204 = "Returned when an existing task has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/finish/{id}", name="finish")
     * @Method({"POST","GET"})
     * @return RedirectResponse
     */
    public function finishAction(Request $request, Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $task->setDateFinished(new \DateTime('now'));
        $task->setStatus('3');
        $em->flush();

        return $this->redirectToRoute('all');

    }

    /**
     * Delay task
     * @param Request $request
     * @param Task $task
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     section="Task",
     *     input="AppBundle\Form\Type\TaskType",
     *     output="AppBundle\Entity\Task",
     *     statusCodes={
     *         204 = "Returned when an existing task has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/delay/{id}", name="delay")
     * @Method({"POST","GET"})
     * @return RedirectResponse
     */
    public function delayAction(Request $request, Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $task->setStatus('4');
        $em->flush();
        return $this->redirectToRoute('all');

    }

    /**
     * Delete task
     * @param int $task
     * @return View
     *
     * @ApiDoc(
     *     section="Task",
     *     statusCodes={
     *         204 = "Returned when an existing Task has been successful deleted",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/delete/{task}", name="delete")
     * @Method({"DELETE", "GET"})
     */
    public function deleteAction($task)
    {
        if ($task === null) {
            return $this->redirectToRoute('all');
        }
        $em = $this->getDoctrine()->getManager();
        $delTask=$this->getTaskRepository()->safeDeleteQuery($task)->getResult();
        if (!$delTask) {
            throw $this->createNotFoundException('No found for id '.$task);
        }

        return $this->redirectToRoute('all');
    }

    /**
     * Delete task
     * @param int $task
     * @return View
     *
     * @ApiDoc(
     *     section="Task",
     *     statusCodes={
     *         204 = "Returned when an existing Task has been successful deleted",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/get", name="gets")
     * @Method({"GET"})
     */
    public function getByEmployeeAction(Request $request)
    {
        $projects= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project')->findAll();
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();

        $tasks=$this->getTaskRepository()->findByEmployeeQuery($userId)->getResult();
        $intasks=$this->getTaskRepository()->findByEmployeeInQuery($userId)->getResult();
        $donetasks=$this->getTaskRepository()->findByEmployeeDoneQuery($userId)->getResult();
        $delaytasks=$this->getTaskRepository()->findByEmployeeDelayQuery($userId)->getResult();

        return $this->render('Task/gets.html.twig', [
            'tasks' => $tasks, 'inprogress'=>$intasks,  'donetasks'=>$donetasks, 'delaytasks'=>$delaytasks, 'projects'=>$projects
        ]);
    }

    /**
     * @return TaskRepository
     */
    private function getTaskRepository()
    {
        return $this->get('crv.doctrine_entity_repository.task');
    }

    private function getCommentRepository()
    {
        return $this->get('crv.doctrine_entity_repository.comment');
    }
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }



}
