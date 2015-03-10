<?php
namespace Ibw\JobeetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('IbwJobeetBundle:Category')->findOneBySlug($slug);
        if (!$category) {
            throw $this->createNotFoundException('无法找到Category实体');
        }
        $category->setActiveJobs($em->getRepository('IbwJobeetBundle:Job')->getActiveJobs($category->getId()));
        return $this->render('IbwJobeetBundle:Category:show.html.twig', array(
            'category' => $category
        ));
    }
}