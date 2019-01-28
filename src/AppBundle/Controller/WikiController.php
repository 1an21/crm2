<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Repository\WikiRepository;
use AppBundle\Form\Type\WikiType;
use AppBundle\Entity\Wiki;
use AppBundle\Entity\Project;
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
 * Class WikiController
 * @package AppBundle\Controller
 *
 * @RouteResource("Wiki")
 */
class WikiController extends FOSRestController implements ClassResourceInterface
{

    /**
     * view Wiki
     * @Route("/wiki/", name="wiki")
     * @Method({"GET"})
     * @return
     */
    public function wikiAction( )
    {
        $projects= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project')->findAll();
        $wiki=$this->getWikiRepository()->getWikiQuery()->getResult();
        return $this->render('Wiki/wiki.html.twig', [
            'wiki'=>$wiki, 'projects'=>$projects
        ]);
    }

    /**
     * view Wiki
     * @Route("/wiki/q/{project}", name="wiki-all")
     * @Method({"GET"})
     * @return
     */
    public function allWikiAction(Project $project)
    {
        $projects= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project')->findAll();
        $project_id=$project->getId();
        $wiki=$this->getWikiRepository()->getWikiAllQuery($project_id)->getResult();
        return $this->render('Wiki/wiki-all.html.twig', [
            'wiki'=>$wiki, 'projects'=>$projects
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
     * @Route("/wiki/create", name="wiki-create")
     * @Method({"POST", "GET"})
     */
    public function postAction(Request $request)
    {
        $projects= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project')->findAll();
        $form = $this->createForm(WikiType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $wiki = $form->getData();
            if($wiki->getFile()!=null) {
                $file = $wiki->getFile();
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                $file->move(
                    $this->getParameter('image_directory'),
                    $fileName
                );

                $wiki->setFile($fileName);
            }
            $em->persist($wiki);
            $em->flush();
            return $this->redirectToRoute('wiki');
        }
        return $this->render('Wiki/create.html.twig', [
            'form' => $form->createView(), 'projects'=>$projects
        ]);
    }

    /**
     * Edit wiki
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
     * @Route("/wiki/edit/{id}", name="edit-wiki")
     * @Method({"POST","GET"})
     * @return RedirectResponse
     */
    public function editAction(Request $request, Wiki $wiki)
    {
        $projects= $this->getDoctrine()->getManager()->getRepository('AppBundle:Project')->findAll();
        $img=$wiki->getFile();
        if($img !== null) {
            $wiki->setFile(new File($this->getParameter('image_directory').'/'.$img));
        }
        $form = $this->createForm(WikiType::class, $wiki);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->redirectToRoute('wiki');
        }
        return $this->render('Wiki/edit.html.twig', [
            'form' => $form->createView(), 'projects'=>$projects
        ]);
    }

    private function getWikiRepository()
    {
        return $this->get('crv.doctrine_entity_repository.wiki');
    }
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}
