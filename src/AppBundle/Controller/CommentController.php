<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Taskger\TaskBundle\Entity\Comment;
use Taskger\TaskBundle\Form\CommentType;

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

/**
 * Comment controller.
 */
class CommentController extends Controller
{
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
     * @Route("/createss", name="screate")
     * @Method({"POST", "GET"})
     */
    public function postAction(Request $request)
    {
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $task = $form->getData();

            $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
            $userCreated = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find( $userId);
            $task->setUser($userCreated);
            $em->persist($task);
            $em->flush();
            return $this->redirectToRoute('all');
        }
        return $this->render('Task/view.html.twig', [
            'form' => $form->createView()
        ]);
    }

}


