<?php

namespace App\Twig;


use Detection\MobileDetect;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
class MobileDetectExtension extends AbstractExtension
{
    private MobileDetect $mobileDetect;

    public function __construct(MobileDetect $mobileDetect)
    {
        $this->mobileDetect = $mobileDetect;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_mobile', [$this, 'isMobile']),
        ];
    }

    public function isMobile(): bool
    {
        return $this->mobileDetect->isMobile();
    }

}
