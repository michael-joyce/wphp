<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\BlogBundle\Entity\Page;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Default controller for the home page.
 */
class DefaultController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Home page.
     *
     * @Route("/", name="homepage")
     * @Template()
     *
     * @return array
     */
    public function indexAction(EntityManagerInterface $em) {
        $repo = $em->getRepository(Page::class);
        $page = $repo->findHomepage();
        return [
            'homepage' => $page,
        ];
    }

    /**
     * Privacy page.
     *
     * @Route("/privacy", name="privacy")
     * @Template()
     *
     * @return array
     */
    public function privacyAction(Request $request) {
        return [];
    }
}
