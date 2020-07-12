<?php

namespace App\Twig;

use Carbon\Carbon;
use DateTimeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CarbonExtension extends AbstractExtension
{
    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('carbon', [$this, 'getCarbon']),
            new TwigFilter('diffForHumans', [$this, 'getDiffForHumans'])
        ];
    }

    public function getCarbon(DateTimeInterface $dateTime): Carbon
    {
        return new Carbon($dateTime);
    }

    public function getDiffForHumans(DateTimeInterface $dateTime, ?string $locale = null): string
    {
        return $this->getCarbon($dateTime)->locale($locale ?: $this->locale)->diffForHumans();
    }
}
