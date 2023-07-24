<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\CatalogueItem;
use App\Form\Type\CatalogueItemType;
use App\Service\CurrencyConverter;

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

            $costString = $form->get('cost')->getData();

            $costInPence = $catalogueItem->parseCost($costString);

            $catalogueItem->setCost($costInPence);

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
     * @Route("/catalogue/{identificationCode}/edit", name="catalogue_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CatalogueItem $catalogueItem, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $form = $this->createForm(CatalogueItemType::class, $catalogueItem);
        $form->handleRequest($request);

        $csrfToken = $csrfTokenManager->getToken('delete' . $catalogueItem->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $costString = $form->get('cost')->getData();

            $costInPence = $catalogueItem->parseCost($costString);

            $catalogueItem->setCost($costInPence);

            $entityManager->persist($catalogueItem);
            $entityManager->flush();

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
     * @Route("/catalogue/{identificationCode}", methods={"POST"}, name="catalogue_delete")
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
        return $this->createFormBuilder(null, [
                'method' => 'DELETE',
                'action' => $this->generateUrl('catalogue_delete', ['identificationCode' => $catalogueItem->getIdentificationCode()]),
            ])
            ->add('token', HiddenType::class, [
                'data' => $csrfToken,
            ])
            ->getForm();
    }

    /**
     * @Route("/catalogue/{identificationCode}", methods={"GET"}, name="catalogue_read_item")
     */
    public function readItem(string $identificationCode, SerializerInterface $serializer): JsonResponse
    {
        $catalogueItem = $this->doctrine->getRepository(CatalogueItem::class)->findOneBy(['identificationCode' => $identificationCode]);

        if (!$catalogueItem) {
            return new JsonResponse(['error' => 'Item not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $json = $serializer->serialize($catalogueItem, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/catalogue", methods={"GET"}, name="catalogue_api")
     */
    public function getAllItems(SerializerInterface $serializer): JsonResponse
    {
        $catalogueItems = $this->doctrine->getRepository(CatalogueItem::class)->findAll();
        $json = $serializer->serialize($catalogueItems, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @Route("/catalogue/{identificationCode}/add/{amount}", methods={"[GET, POST]"}, name="catalogue_add")
     */
    public function addCost(CatalogueItem $catalogueItem, string $amount, EntityManagerInterface $entityManager): Response
    {
        // Regular expression to match the format "XpYsZd"
        $pattern = '/^(\d+)p(\d+)s(\d+)d$/';

        if (preg_match($pattern, $amount, $matches)) {
            $pounds = (int)$matches[1];
            $shillings = (int)$matches[2];
            $pence = (int)$matches[3];

            // Convert pounds and shillings to pence and add to the total cost
            $totalPence = ($pounds * 240) + ($shillings * 12) + $pence;

            $oldCost = $catalogueItem->getCost();
            // Update the CatalogueItem entity with the new total cost in pence
            $catalogueItem->setCost($catalogueItem->getCost() + $totalPence);

            // Persist and flush the changes to the database
            $entityManager->persist($catalogueItem);
            $entityManager->flush();

            return new Response(
                'Cost added successfully. Value was ' . CurrencyConverter::penceToString($oldCost) .
                ' || ' . $catalogueItem->getName() . ' new cost value is ' . CurrencyConverter::penceToString($catalogueItem->getCost())
            );
        }

        return new Response('Invalid format for amount. Has to be "XpYsZd" Where X is pounds amount, Y is shillings amount and Z is pence amount', 400);
    }

    /**
     * @Route("/catalogue/{id}/subtract/{amount}", methods={"POST"}, name="catalogue_subtract")
     */
    public function subtractCost(CatalogueItem $catalogueItem, string $amount, EntityManagerInterface $entityManager): Response
    {
        $pattern = '/^(\d+)p(\d+)s(\d+)d$/';

        if (preg_match($pattern, $amount, $matches)) {
            $pounds = (int)$matches[1];
            $shillings = (int)$matches[2];
            $pence = (int)$matches[3];

            $totalPence = ($pounds * 240) + ($shillings * 12) + $pence;

            $oldCost = $catalogueItem->getCost();
            $catalogueItem->setCost($catalogueItem->getCost() - $totalPence);

            $entityManager->persist($catalogueItem);
            $entityManager->flush();

            return new Response(
                'Cost subtracted successfully. Value was: ' . CurrencyConverter::penceToString($oldCost) .
                ' || ' . $catalogueItem->getName() . ' new cost value is: ' . CurrencyConverter::penceToString($catalogueItem->getCost())
            );
        }

        return new Response('Invalid format for amount. Has to be "XpYsZd" Where X is pounds amount, Y is shillings amount and Z is pence amount', 400);
    }

    /**
     * @Route("/catalogue/{id}/multiply/{multiplier}", methods={"POST"}, name="catalogue_multiply")
     */
    public function multiplyCost(CatalogueItem $catalogueItem, int $multiplier, EntityManagerInterface $entityManager): Response
    {
        if (is_integer($multiplier)) {
            $pence = $catalogueItem->getCost();
            $newPence = $pence * $multiplier;
            $oldCost = $catalogueItem->getCost();

            $catalogueItem->setCost($newPence);

            $entityManager->flush();

            return new Response(
                'Cost multiplied successfully. Value was ' . CurrencyConverter::penceToString($oldCost) .
                ' || ' . $catalogueItem->getName() . ' new cost value is ' . CurrencyConverter::penceToString($catalogueItem->getCost())
            );
        }

        return new Response('Invalid format for multiplier. Has to be valid integer', 400);
    }
}