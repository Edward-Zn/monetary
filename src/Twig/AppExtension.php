<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('format_cost', [$this, 'formatCost']),
        ];
    }

    public function formatCost(int $pence): string
    {
        $p = floor($pence / 240);
        $s = floor(($pence % 240) / 12);
        $d = $pence % 12;

        return "$p" . "p " . "$s" . "s " . "$d" . "d";
    }
}