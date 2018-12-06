<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\Repository\ProjectRepository;
use AppBundle\Form\Type\ProjectType;
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
 * Class ProjectController
 * @package AppBundle\Controller
 *
 * @RouteResource("Project")
 */
class ProjectController extends FOSRestController implements ClassResourceInterface
{
    

    /**
     * Add a new Project
     * @param Request $request
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     section="Project",
     *     output="AppBundle\Entity\Project",
     *     statusCodes={
     *         201 = "Returned when a new Project has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/project/create", name="create-project")
     * @Method({"POST", "GET"})
     */
    public function postAction(Request $request)
    {
        $form = $this->createForm(ProjectType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $Project = $form->getData();

            $em->persist($Project);
            $em->flush();
            return $this->redirectToRoute('all');
        }
        return $this->render('Project/create.html.twig', [
            'form' => $form->createView()
        ]);
    }



    /**
     * @return ProjectRepository
     */
    private function getProjectRepository()
    {
        return $this->get('crv.doctrine_entity_repository.project');
    }
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }



}
