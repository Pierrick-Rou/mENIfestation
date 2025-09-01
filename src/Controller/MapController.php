<?php

namespace App\Controller;


use App\DTO\SortieMapDTO;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Map\Bridge\Leaflet\LeafletOptions;
use Symfony\UX\Map\Bridge\Leaflet\Option\TileLayer;
use Symfony\UX\Map\InfoWindow;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;



class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function __invoke(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->getInfoForMap();

        // Centrage de la carte sur un point (ici Lyon par défaut)
        $map = (new Map('default'))
            ->center(new Point(45.7534031, 4.8295061))
            ->zoom(6);

        foreach ($sorties as $sortie) {
            $position = new Point($sortie['latitude'], $sortie['longitude']);
            $title = $sortie['nom'];

            // Prépare un contenu HTML pour la popup (InfoWindow)
            $content = sprintf(
                '<h3>%s</h3><p>%s</p><p><strong>Lieu:</strong> %s, %s %s</p><p><strong>Date:</strong> %s</p>',
                htmlspecialchars("{$sortie['nom']}"),
                nl2br(htmlspecialchars($sortie['infos'])),
                htmlspecialchars($sortie['lieu']),
                htmlspecialchars($sortie['codePostal']),
                htmlspecialchars($sortie['ville']),
                $sortie['date']->format('d/m/Y H:i')
            );

            $map->addMarker(
                new Marker(
                    position: $position,
                    title: $title,
                    infoWindow: new InfoWindow(content: $content)
                )
            );
        }

        // Ajout des options de la carte (tuiles OpenStreetMap)
        $map->options((new LeafletOptions())
            ->tileLayer(new TileLayer(
                url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                options: ['maxZoom' => 19]
            ))
        );

        return $this->render('map/index.html.twig', [
            'map' => $map,
        ]);
    }
}
