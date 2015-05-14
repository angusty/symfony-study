<?php

namespace Ibw\JobeetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Ibw\JobeetBundle\Entity\User;
use Ibw\JobeetBundle\Entity\Job;
use Ibw\JobeetBundle\Form\JobType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Job controller.
 *
 */
class JobController extends Controller
{

    public function testAction(Request $request)
    {
        //$em = $this->container->get('doctrine')->getManager();
//        $username = 'admin_test';
//        $password = 'admin';
//        $user = new User;
//        $user->setUsername($username);
//        //encode the password
//        $factory = $this->container->get('security.encoder_factory');
//        $encoder  = $factory->getEncoder($user);
//        $encodedPassword = $encoder->encodePassword($password, $user->getSalt());
//        ladybug_dump($encoder);
//        $pass = 'admin';
//        $password = hash('sha512', $pass, true);
//        for ($i = 1; $i < 5000; $i++) {
//            $password = hash('sha512', $password . $pass, true);
//        }
//        echo base64_encode($password);
//        $one = $this->getRequest()->getRequestFormat('jsp');
//        $get = $request->query->get('name');
//        $post = $request->request->get('name');
//        echo 'test: ', $post;
        //ladybug_dump($request);
        $message = \Swift_Message::newInstance()
            ->setSubject('邮件发送测试x')
            ->setFrom('yboker1982@gmail.com')
            ->setTo('8236138@qq.com')
            ->setBody('<div>邮件发送测试</div>');

        $return = $this->get('mailer')->send($message);
        ladybug_dump($return);
        $response = new Response('<div>发送邮件设置</div>');
        return $response;
    }
    /**
     * Lists all Job entities.
     *
     */
    public function indexAction()
    {
        $factory = $this->container->get('security.encoder_factory');
        //ladybug_dump($factory);
        //echo hash('sha512', 'admin');
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
        $latestJob = $em->getRepository('IbwJobeetBundle:Job')->getLatestPost();
        if ($latestJob) {
            $lastUpdated = $latestJob
                ->getCreatedAt()
                ->format(DATE_ATOM);
        } else {
            $lastUpdated = new \DateTime();
            $lastUpdated = $lastUpdated->format(DATE_ATOM);
        }
        $format = $this->getRequest()->getRequestFormat();
        return $this->render('IbwJobeetBundle:Job:index.' . $format . '.twig', array(
            'categories' => $categories,
            'lastUpdated' => $lastUpdated,
            'feedId' => sha1($this->get('router')->generate('ibw_job', array('_format'=>'atom'), true))
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

            return $this->redirect($this->generateUrl('ibw_job_preview', array(
                'company' => $entity->getCompanySlug(),
                'location' => $entity->getLocationSlug(),
                'token' => $entity->getToken(),
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
        $session = $this->getRequest()->getSession();
        $jobs = $session->get('job_history', array());
        //ladybug_dump($jobs);
        $job = array('id'=>$entity->getId(), 'position'=>$entity->getPosition(), 'company'=>$entity->getCompany(),'companyslug' => $entity->getCompanySlug(), 'locationslug' => $entity->getLocationSlug(), 'positionslug' => $entity->getPositionSlug());
        if (!in_array($job, $jobs)) {
            array_unshift($jobs, $job);
            $session->set('job_history', array_slice($jobs,0, 3));
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

        $entity = $em->getRepository('IbwJobeetBundle:Job')->findOneByToken($token);
 //       $entity = $em->getRepository('IbwJobeetBundle:Job')->getActiveJob($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $deleteForm = $this->createDeleteForm($entity->getToken());
        $pulishForm = $this->createPublishForm($entity->getToken());
        return $this->render('IbwJobeetBundle:Job:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'push_form'  => $pulishForm->createView()
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

            return $this->redirect($this->generateUrl('ibw_job_preview', array(
                'token' => $token,
                'company' => $entity->getCompanySlug(),
                'location' => $entity->getLocationSlug(),
                'position' => $entity->getPositionSlug()
            )));
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

    public function publishAction(Request $request, $token)
    {
        $form = $this->createPublishForm($token);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('IbwJobeetBundle:Job')->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Job entity.');
            }

            $entity->publish();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Your job is now online for 30 days.');
        }

        return $this->redirect($this->generateUrl('ibw_job_preview', array(
            'company' => $entity->getCompanySlug(),
            'location' => $entity->getLocationSlug(),
            'token' => $entity->getToken(),
            'position' => $entity->getPositionSlug()
        )));
    }


    private function createPublishForm($token)
    {
        return $this->createFormBuilder(array('token'=>$token))
            ->add('token', 'hidden')
            ->getForm();
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
