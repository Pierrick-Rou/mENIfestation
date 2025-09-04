<?php


namespace App\Service;

use App\Entity\Sortie;
use Symfony\UX\Map\Bridge\Leaflet\LeafletOptions;
use Symfony\UX\Map\Bridge\Leaflet\Option\TileLayer;
use Symfony\UX\Map\InfoWindow;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;

class CarteService
{
    public function buildMap(array $sortieList): Map
    {
        $map = (new Map('default'))
            ->center(new Point(45.7534031, 4.8295061)) // tu peux rendre ça configurable
            ->zoom(6);

        foreach ($sortieList as $sortie) {
            if ($this->hasValidCoords($sortie)) {
                $map->addMarker($this->createMarker($sortie));
            }
        }

        $map->options((new LeafletOptions())
            ->tileLayer(new TileLayer(
                url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                options: ['maxZoom' => 19]
            ))
        );

        return $map;
    }

    private function hasValidCoords(Sortie $sortie): bool
    {
        return $sortie->getLieu()
            && $sortie->getLieu()->getLatitude() !== null
            && $sortie->getLieu()->getLongitude() !== null;
    }

    private function createMarker(Sortie $sortie): Marker
    {
        $position = new Point(
            $sortie->getLieu()->getLatitude(),
            $sortie->getLieu()->getLongitude()
        );

        $title = $sortie->getNom();

        $content = sprintf(
            '<h5><a href="%s">%s</a></h5>
             <p>%s</p>
             <p><strong>Lieu:</strong> %s, %s %s</p>
             <p><strong>Date:</strong> %s</p>',
            htmlspecialchars('sortie/' . $sortie->getId()),
            htmlspecialchars($sortie->getNom()),
            nl2br(htmlspecialchars($sortie->getInfosSortie())),
            htmlspecialchars($sortie->getLieu()->getNom()),
            htmlspecialchars($sortie->getLieu()->getVille()->getCodePostal()),
            htmlspecialchars($sortie->getLieu()->getVille()->getNom()),
            $sortie->getDateHeureDebut()->format('d/m/Y H:i')
        );

        return new Marker(
            position: $position,
            title: $title,
            infoWindow: new InfoWindow(content: $content)
        );
    }
}
