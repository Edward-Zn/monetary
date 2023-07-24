<?php

namespace App\Service;

class CurrencyConverter
{
    public static function stringToPence(string $amount): int
    {
        // Regular expression to match the format "XpYsZd"
        $pattern = '/^(\d+)p(\d+)s(\d+)d$/';
    
        if (preg_match($pattern, str_replace(' ', '', $amount), $matches)) {
            $pounds = (int)$matches[1];
            $shillings = (int)$matches[2];
            $pence = (int)$matches[3];
    
            // Convert pounds and shillings to pence and calculate the total pence
            $totalPence = ($pounds * 240) + ($shillings * 12) + $pence;
    
            return $totalPence;
        }

        throw new \InvalidArgumentException('Invalid format for amount. Format should be "XpYsZd".');
    }

    public static function penceToString(int $pence): string
    {
        // Calculate the amount in pounds, shillings, and pence
        $pounds = (int)($pence / 240);
        $remainderPence = $pence % 240;
        $shillings = (int)($remainderPence / 12);
        $pence = $remainderPence % 12;

        // Build the string representation in the format "Xp Ys Zd"
        $amountString = "{$pounds}p {$shillings}s {$pence}d";

        return $amountString;
    }
}