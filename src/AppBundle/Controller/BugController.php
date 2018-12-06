<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bug;
use AppBundle\Entity\Repository\BugRepository;
use AppBundle\Form\Type\BugType;
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
 * Class BugController
 * @package AppBundle\Controller
 *
 * @RouteResource("Bug")
 */
class BugController extends FOSRestController implements ClassResourceInterface
{
//    /**
//     * Gets a tree of Bugs
//     *
//     * @return array
//     *
//     * @ApiDoc(
//     *     section="Bug",
//     *     output="AppBundle\Entity\Bug",
//     *     statusCodes={
//     *         200 = "Returned when successful",
//     *         404 = "Return when not found"
//     *     }
//     * )
//     *
//     * @Route("/", name="index")
//     * @Method("GET")
//     */
//
//    public function cgetAction(Request $request)
//    {
//        $queryBuilder = $this->getBugRepository()->searchQuery();
//        if ($request->query->getAlnum('filter')) {
//            $queryBuilder->where('e.title LIKE :name')
//                ->orwhere('e.surname LIKE :name')
//
//                ->setParameter('name', '%' . $request->query->getAlnum('filter') . '%');
//        }
//        $query = $queryBuilder->getQuery();
//        $paginator  = $this->get('knp_paginator');
//        $bugs = $paginator->paginate(
//            $query,
//            $request->query->getInt('page', 1),
//            $request->query->getInt('limit', 100)
//        );
//        return $this->render('Bug/index.html.twig', [
//            'bugs' => $bugs,
//        ]);
//    }
    /**
     * Gets a collection of Bugs
     *
     * @return array
     *
     * @ApiDoc(
     *     section="Bug",
     *     output="AppBundle\Entity\Bug",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     *
     * @Route("/bug/", name="all-bug")
     * @Method("GET")
     */

    public function allGetAction(Request $request)
    {

        $queryBuilder = $this->getBugRepository()->searchQuery();
        if ($request->query->getAlnum('filter')) {
            $queryBuilder
                ->where('e.title LIKE :name')
                ->setParameter('name', '%' . $request->query->getAlnum('filter') . '%');
        }
        $query = $queryBuilder->getQuery();
        $paginator  = $this->get('knp_paginator');
        $bugs = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );
        $inbugs = $this->getBugRepository()->findInProgressQuery()->getResult();
        $donebugs = $this->getBugRepository()->findDoneQuery()->getResult();
        $delaybugs = $this->getBugRepository()->findDelayQuery()->getResult();
        return $this->render('Bug/all.html.twig', [
            'bugs' => $bugs, 'inprogress'=>$inbugs, 'donebugs'=>$donebugs, 'delaybugs'=>$delaybugs
        ]);
    }

    /**
     * Gets a collection of Bugs
     *
     * @return array
     *
     * @ApiDoc(
     *     section="Bug",
     *     output="AppBundle\Entity\Bug",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     *
     * @Route("/bug/trash", name="alltrash-bug")
     * @Method("GET")
     */

    public function allTrashAction(Request $request)
    {

        $queryBuilder = $this->getBugRepository()->searchTrashQuery();
        if ($request->query->getAlnum('filter')) {
            $queryBuilder
                ->where('e.title LIKE :name')
                ->orwhere('e.description LIKE :name')
                ->setParameter('name', '%' . $request->query->getAlnum('filter') . '%');
        }
        $query = $queryBuilder->getQuery();
        $paginator  = $this->get('knp_paginator');
        $bugs = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );
        return $this->render('Bug/alltrash.html.twig', [
            'bugs' => $bugs,
        ]);
    }


    /**
     * Add a new bug
     * @param Request $request
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     section="Bug",
     *     output="AppBundle\Entity\Bug",
     *     statusCodes={
     *         201 = "Returned when a new bug has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/bug/create", name="create-bug")
     * @Method({"POST", "GET"})
     */
    public function postAction(Request $request)
    {
        $form = $this->createForm(BugType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $bug = $form->getData();
            if($bug->getImage()!=null) {
                $file = $bug->getImage();
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                $file->move(
                    $this->getParameter('image_directory'),
                    $fileName
                );

                $bug->setImage($fileName);
            }
            $em->persist($bug);
            $em->flush();
            return $this->redirectToRoute('all-bug');
        }
        return $this->render('Bug/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * get single bug
     * @param Request $request
     * @param Bug $bug
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     section="Bug",
     *     input="AppBundle\Form\Type\BugType",
     *     output="AppBundle\Entity\Bug",
     *     statusCodes={
     *         204 = "Returned when an existing bug has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/bug/view/{id}", name="view-bug")
     * @Method({"GET"})
     * @return RedirectResponse
     */
    public function getAction(Bug $bug)
    {
        //$bugs= $this->getDoctrine()->getManager()->find($bug);


        return $this->render('Bug/view.html.twig', [
            'bug' => $bug,
        ]);
    }

    /**
     * Start bug
     * @param Request $request
     * @param Bug $bug
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     section="Bug",
     *     input="AppBundle\Form\Type\BugType",
     *     output="AppBundle\Entity\Bug",
     *     statusCodes={
     *         204 = "Returned when an existing bug has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/bug/edit/{id}", name="edit-bug")
     * @Method({"POST","GET"})
     * @return RedirectResponse
     */
    public function patchAction(Request $request, Bug $bug)
    {
        $em = $this->getDoctrine()->getManager();
        $bug->setDateStarted(new \DateTime('now'));
        $bug->setStatus('2');
        $em->flush();

        return $this->redirectToRoute('all-bug');
    }

    /**
     * Finish bug
     * @param Request $request
     * @param Bug $bug
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     section="Bug",
     *     input="AppBundle\Form\Type\BugType",
     *     output="AppBundle\Entity\Bug",
     *     statusCodes={
     *         204 = "Returned when an existing bug has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/bug/finish/{id}", name="finish-bug")
     * @Method({"POST","GET"})
     * @return RedirectResponse
     */
    public function finishAction(Request $request, Bug $bug)
    {
        $em = $this->getDoctrine()->getManager();
        $bug->setDateFinished(new \DateTime('now'));
        $bug->setStatus('3');
        $em->flush();

        return $this->redirectToRoute('all-bug');

    }

    /**
     * Delay bug
     * @param Request $request
     * @param Bug $bug
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     section="Bug",
     *     input="AppBundle\Form\Type\BugType",
     *     output="AppBundle\Entity\Bug",
     *     statusCodes={
     *         204 = "Returned when an existing bug has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/bug/delay/{id}", name="delay-bug")
     * @Method({"POST","GET"})
     * @return RedirectResponse
     */
    public function delayAction(Request $request, Bug $bug)
    {
        $em = $this->getDoctrine()->getManager();
        $bug->setStatus('4');
        $em->flush();
        return $this->redirectToRoute('all-bug');

    }

    /**
     * Delete bug
     * @param int $bug
     * @return View
     *
     * @ApiDoc(
     *     section="Bug",
     *     statusCodes={
     *         204 = "Returned when an existing Bug has been successful deleted",
     *         404 = "Return when not found"
     *     }
     * )
     * @Route("/bug/delete/{bug}", name="delete-bug")
     * @Method({"DELETE", "GET"})
     */
    public function deleteAction($bug)
    {
        if ($bug === null) {
            return $this->redirectToRoute('all-bug');
        }
        $em = $this->getDoctrine()->getManager();
        $delBug=$this->getBugRepository()->safeDeleteQuery($bug)->getResult();
        if (!$delBug) {
            throw $this->createNotFoundException('No found for id '.$bug);
        }

        return $this->redirectToRoute('all-bug');
    }

    /**
     * @return BugRepository
     */
    private function getBugRepository()
    {
        return $this->get('crv.doctrine_entity_repository.bug');
    }
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}
