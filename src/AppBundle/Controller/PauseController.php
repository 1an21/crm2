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
        $this->getPauseRepository()->finishQuery($task_id)->getResult();


        return $this->redirectToRoute('all');
    }

    private function getPauseRepository()
    {
        return $this->get('crv.doctrine_entity_repository.pause');
    }
}
