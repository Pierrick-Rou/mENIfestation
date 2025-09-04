const map = window.maps['mapPreview'];

function updateMap(lat, lng, popupContent = '') {
    if (!map) return;

    // Supprime tous les markers existants
    map.eachLayer(layer => {
        if (layer instanceof L.Marker) {
            map.removeLayer(layer);
        }
    });

    // Ajoute le nouveau marker
    const marker = L.marker([lat, lng]).addTo(map);

    if (popupContent) {
        marker.bindPopup(popupContent).openPopup();
    }

    // Centre la carte sur le marker
    map.setView([lat, lng], 14);
}
