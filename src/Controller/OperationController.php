<?php

// src/Controller/CatalogueController.php
// ...

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\OperationItem;

class OperationController extends AbstractController
{
    // CRUD operations for operation items

    /**
     * Perform arithmetic operations.
     *
     * @Route("/Operation/arithmetic/{operation}", methods={"POST"})
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

    private function performAddition(string $value1, string $value2): int
    {
        return (int) $value1 - (int) $value2;
    }

    private function performSubtraction(string $value1, string $value2): int
    {
        return (int) $value1 - (int) $value2;
    }

    private function performMultiplication(string $value, int $multiplier): int
    {
        return (int) $value * $multiplier;
    }
}