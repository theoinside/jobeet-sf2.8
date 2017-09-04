<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class CategoryController
 */
class CategoryController extends Controller
{
    /**
     * @param string $slug
     * @param int $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($slug, $page)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        /** @var Category $category */
        $category = $em
            ->getRepository('AppBundle:Category')
            ->findOneBySlug($slug)
        ;

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $total_jobs = $em
            ->getRepository('AppBundle:Job')
            ->countActiveJobs($category->getId())
        ;
        $jobs_per_page = $this->getParameter('max_jobs_on_category');            ;
        $last_page = ceil($total_jobs / $jobs_per_page);
        $previous_page = ($page > 1) ? ($page - 1) : 1;
        $next_page = ($page < $last_page) ? ($page + 1) : $last_page;
        $category->setActiveJobs(
            $em
                ->getRepository('AppBundle:Job')
                ->getActiveJobs(
                    $category->getId(),
                    $jobs_per_page,
                    ($page - 1) * $jobs_per_page
                )
        );

        return $this->render('AppBundle:Category:show.html.twig', [
            'category' => $category,
            'last_page' => $last_page,
            'previous_page' => $previous_page,
            'current_page' => $page,
            'next_page' => $next_page,
            'total_jobs' => $total_jobs,
        ]);
    }
}

