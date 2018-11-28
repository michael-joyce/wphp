<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Source;
use AppBundle\Form\SourceType;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Source controller.
 *
 * @Route("/source")
 */
class SourceController extends Controller implements PaginatorAwareInterface {

    use PaginatorTrait;

    /**
     * Lists all Source entities.
     *
     * @Route("/", name="source_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Source e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $sources = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);

        return array(
            'sources' => $sources,
            'repo' => $em->getRepository(Source::class),
        );
    }

    /**
     * @param Request $request
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/typeahead", name="source_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeaheadAction(Request $request) {
        $q = $request->query->get('q');
        if( ! $q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Source::class);
        $data = [];
        foreach($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => $result->getName(),
            ];
        }

        return new JsonResponse($data);
    }
    /**
     * Creates a new Source entity.
     *
     * @Route("/new", name="source_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request) {
        $source = new Source();
        $form = $this->createForm(SourceType::class, $source);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($source);
            $em->flush();

            $this->addFlash('success', 'The new source was created.');
            return $this->redirectToRoute('source_show', array('id' => $source->getId()));
        }

        return array(
            'source' => $source,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Source entity.
     *
     * @Route("/{id}", name="source_show")
     * @Method("GET")
     * @Template()
     * @param Source $source
     */
    public function showAction(Request $request, Source $source) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT t FROM AppBundle:Title t WHERE t.source = :source OR t.source2 = :source ORDER BY t.title';
        $query = $em->createQuery($dql);
        $query->setParameter('source', $source);
        $titles = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);

        return array(
            'source' => $source,
            'titles' => $titles,
        );
    }

    /**
     * Displays a form to edit an existing Source entity.
     *
     * @Route("/{id}/edit", name="source_edit")
     * @Method({"GET", "POST"})
     * @Template()
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @param Request $request
     * @param Source $source
     */
    public function editAction(Request $request, Source $source) {
        $editForm = $this->createForm(SourceType::class, $source);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The source has been updated.');
            return $this->redirectToRoute('source_show', array('id' => $source->getId()));
        }

        return array(
            'source' => $source,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Source entity.
     *
     * @Route("/{id}/delete", name="source_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @param Request $request
     * @param Source $source
     */
    public function deleteAction(Request $request, Source $source) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($source);
        $em->flush();
        $this->addFlash('success', 'The source was deleted.');

        return $this->redirectToRoute('source_index');
    }


    }
