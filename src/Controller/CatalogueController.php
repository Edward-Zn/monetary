<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Entity\CatalogueItem;
use App\Form\CatalogueItemType;

class CatalogueController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/catalogue", name="catalogue_list", methods={"GET"})
     */
    public function index(): Response
    {
        $catalogueItems = $this->doctrine->getRepository(CatalogueItem::class)->findAll();

        return $this->render('catalogue/index.html.twig', [
            'catalogueItems' => $catalogueItems,
        ]);
    }

    /**
     * Create a new catalogue item
     * 
     * @Route("/catalogue/create", name="catalogue_create", methods={"GET", "POST"})
     */
    public function create(Request $request): Response
    {
        $catalogueItem = new CatalogueItem();
        $form = $this->createForm(CatalogueItemType::class, $catalogueItem);
        $form->handleRequest($request);

        $catalogueItem->prepareForPersist();

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($catalogueItem);
            $entityManager->flush();

            return $this->redirectToRoute('catalogue_list');
        }

        return $this->render('catalogue/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Update an existing catalogue item
     * 
     * @Route("/catalogue/{id}/edit", name="catalogue_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CatalogueItem $catalogueItem, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $form = $this->createForm(CatalogueItemType::class, $catalogueItem);
        $form->handleRequest($request);

        $csrfToken = $csrfTokenManager->getToken('delete' . $catalogueItem->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->flush();

            return $this->redirectToRoute('catalogue_list');
        }

        return $this->render('catalogue/edit.html.twig', [
            'form' => $form->createView(),
            'catalogueItem' => $catalogueItem,
            'delete_form' => $this->createDeleteForm($catalogueItem, $csrfToken),
        ]);
    }

    /**
     * Delete a catalogue item
     * 
     * @Route("/catalogue/{id}", methods={"POST"}, name="catalogue_delete")
     */
    public function delete(Request $request, CatalogueItem $catalogueItem): Response
    {
        if ($this->isCsrfTokenValid('delete'.$catalogueItem->getId(), $request->request->get('_token'))) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($catalogueItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('catalogue_list');
    }

    private function createDeleteForm(CatalogueItem $catalogueItem, $csrfToken)
    {
        // Create a form for the delete action
        return $this->createFormBuilder(null, [
                'method' => 'DELETE',
                'action' => $this->generateUrl('catalogue_delete', ['id' => $catalogueItem->getId()]),
            ])
            ->add('token', HiddenType::class, [
                'data' => $csrfToken,
            ])
            ->getForm();
    }

    /**
     * Perform arithmetic operations.
     *
     * @Route("/catalogue/arithmetic/{operation}", methods={"POST"})
     */
    public function arithmetic(Request $request, string $operation): Response
    {
        $data = json_decode($request->getContent(), true);

        $result = null;

        if ($operation === 'addition') {
            $result = $this->performAddition($data['value1'], $data['value2']);
        } elseif ($operation === 'subtraction') {
            $result = $this->performSubtraction($data['value1'], $data['value2']);
        } elseif ($operation === 'multiplication') {
            $result = $this->performMultiplication($data['value'], $data['multiplier']);
        }

        return $this->json(['result' => $result]);
    }

    private function performAddition(string $value1, string $value2): string
    {
        return (string) ($value1 + $value2);
    }

    private function performSubtraction(string $value1, string $value2): string
    {
        return (string) ($value1 - $value2);
    }

    private function performMultiplication(string $value, int $multiplier): string
    {
        return (string) ($value * $multiplier);
    }
}