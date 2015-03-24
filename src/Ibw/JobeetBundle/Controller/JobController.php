<?php

namespace Ibw\JobeetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Ibw\JobeetBundle\Entity\Job;
use Ibw\JobeetBundle\Form\JobType;

/**
 * Job controller.
 *
 */
class JobController extends Controller
{

    /**
     * Lists all Job entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        //\Kint::dump($em);
        //ladybug_dump($em);
        //$entities = $em->getRepository('IbwJobeetBundle:Job')->findAll();
//        $query = $em->createQuery(
//            'SELECT j FROM IbwJobeetBundle:Job j WHERE j.created_at > :date'
//        )->setParameter('date', date('Y-m-d H:i:s', time()-86400*30));
//        $query = $em->createQuery(
//            'SELECT j FROM IbwJobeetBundle:Job j WHERE   j.expires_at> :date'
//        )->setParameter('date', date('Y-m-d H:i:s', time()));
//        $entities = $query->getResult();
//        $entities = $em->getRepository('IbwJobeetBundle:Job')->getActiveJobs();
//        return $this->render('IbwJobeetBundle:Job:index.html.twig', array(
//            'entities' => $entities,
//        ));
        $categories = $em->getRepository('IbwJobeetBundle:Category')->getWithJobs();
        foreach($categories as $category) {

            $category->setActiveJobs($em->getRepository('IbwJobeetBundle:Job')->getActiveJobs($category->getId(), $this->container->getParameter('max_jobs_on_homepage')));
            $category->setMoreJobs($em->getRepository('IbwJobeetBundle:Job')->countActiveJobs($category->getId(), $this->container->getParameter('max_jobs_on_homepage')));
        }
        return $this->render('IbwJobeetBundle:Job:index.html.twig', array(
            'categories' => $categories
        ));
    }
    /**
     * Creates a new Job entity.
     *
     */
    public function createAction(Request $request)
    {
//        $entity = new Job();
//        $form = $this->createCreateForm($entity);
//        $form->handleRequest($request);
//
//        if ($form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($entity);
//            $em->flush();
//
//            return $this->redirect($this->generateUrl('ibw_job_show', array('id' => $entity->getId())));
//        }
//
//        return $this->render('IbwJobeetBundle:Job:new.html.twig', array(
//            'entity' => $entity,
//            'form'   => $form->createView(),
//        ));
        $entity = new Job();
        $form = $this->createForm(new JobType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //$entity->file->move(__DIR__ . '/../../../../web/uploads/jobs', $entity->file->getClientOriginalName());
            //$entity->setLogo($entity->file->getClientOriginalName());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ibw_job_show', array(
                'company' => $entity->getCompanySlug(),
                'location' => $entity->getLocationSlug(),
                'id' => $entity->getId(),
                'position' => $entity->getPositionSlug()
            )));
        }
        return $this->render('IbwJobeetBundle:Job:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),

        ));
    }

    /**
     * Creates a form to create a Job entity.
     *
     * @param Job $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Job $entity)
    {
        $form = $this->createForm(new JobType(), $entity, array(
            'action' => $this->generateUrl('ibw_job_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => '创建'));

        return $form;
    }

    /**
     * Displays a form to create a new Job entity.
     *
     */
    public function newAction()
    {
        $entity = new Job();
        $entity->setType('full-time');
        $form   = $this->createCreateForm($entity);

        return $this->render('IbwJobeetBundle:Job:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Job entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

//        $entity = $em->getRepository('IbwJobeetBundle:Job')->find($id);
        $entity = $em->getRepository('IbwJobeetBundle:Job')->getActiveJob($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('IbwJobeetBundle:Job:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function previewAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IbwJobeetBundle:Job')->findOenByToken($token);
 //       $entity = $em->getRepository('IbwJobeetBundle:Job')->getActiveJob($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $deleteForm = $this->createDeleteForm($entity->getId());

        return $this->render('IbwJobeetBundle:Job:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Job entity.
     *
     */
//    public function editAction($id)
    public function editAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IbwJobeetBundle:Job')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($token);
//        \Kint::dump($editForm->createView());
        return $this->render('IbwJobeetBundle:Job:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Job entity.
    *
    * @param Job $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Job $entity)
    {
        $form = $this->createForm(new JobType(), $entity, array(
            'action' => $this->generateUrl('ibw_job_update', array('token' => $entity->getToken())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => '修改'));

        return $form;
    }
    /**
     * Edits an existing Job entity.
     *
     */
    public function updateAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IbwJobeetBundle:Job')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }
        $editForm = $this->createForm(new JobType(), $entity);
        $deleteForm = $this->createDeleteForm($token);
//        $editForm = $this->createEditForm($entity);
//        $editForm->handleRequest($request);
        $editForm->bind($request);
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ibw_job_edit', array('token' => $token)));
        }

        return $this->render('IbwJobeetBundle:Job:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Job entity.
     *
     */
    public function deleteAction(Request $request, $token)
    {
        $form = $this->createDeleteForm($token);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('IbwJobeetBundle:Job')->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Job entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ibw_job'));
    }

    /**
     * Creates a form to delete a Job entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($token)
    {
        return $this->createFormBuilder(array('token'=>$token))
            ->add('token', 'hidden')
            ->getForm();
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('ibw_job_delete', array('token' => $token)))
//            ->setMethod('DELETE')
//            ->add('submit', 'submit', array('label' => 'Delete'))
//            ->getForm()
//        ;
    }
}
