<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository\PauseRepository;
use AppBundle\Form\Type\PauseType;
use AppBundle\Entity\Pause;
use AppBundle\Entity\Task;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
/**
 * Class PauseController
 * @package AppBundle\Controller
 *
 * @RouteResource("Pause")
 */
class PauseController extends FOSRestController implements ClassResourceInterface
{

    /**
     * Add a new Pause (start pause)
     * @param $task

     * @Route("/pause/{id}", name="pause")
     * @Method({"POST","GET"})
     * @return RedirectResponse
     */
    public function pauseAction(Task $task)
    {
        $pause = new Pause();
        $em = $this->getDoctrine()->getManager();

        $pause->setTask($task);
        $pause->setDateStarted(new \DateTime('now'));
        $pause->setDescription('start');
        $em->persist($pause);
        $em->flush();

        return $this->redirectToRoute('all');
    }

    /**
     * finish Pause
     * @param $task, $pause

     * @Route("/pause-end/{id}", name="pause-end")
     * @Method({"POST","GET"})
     * @return RedirectResponse
     */
    public function finishPauseAction(Task $task)
    {
        $task_id = $task->getId();
        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'UPDATE pause SET date_finished = CURRENT_TIME(), description = "end"
            WHERE task_id= '.$task_id.'  order by id desc limit 1';

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        return $this->redirectToRoute('all');
    }

}
