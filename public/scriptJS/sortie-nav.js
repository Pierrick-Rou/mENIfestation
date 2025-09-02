let leafletMap = null;

// Symfony UX Map envoie un event à chaque connexion de la carte
document.addEventListener("ux:map:connect", (event) => {
    leafletMap = event.detail.map;
});

const btnList = document.getElementById("btn-list");
const btnMap  = document.getElementById("btn-map");
const listView = document.getElementById("list-view");
const mapView  = document.getElementById("map-view");

btnList.addEventListener("click", () => {
    listView.style.display = "block";
    mapView.style.display = "none";
    btnList.classList.add("active");
    btnMap.classList.remove("active");
});

btnMap.addEventListener("click", () => {
    listView.style.display = "none";
    mapView.style.display = "block";
    btnMap.classList.add("active");
    btnList.classList.remove("active");

    // TRÈS IMPORTANT : recalcul Leaflet après affichage
    setTimeout(() => {
        if (leafletMap) {
            leafletMap.invalidateSize();
        }
    }, 150);
});

document.getElementById('btn-carte').addEventListener('click', function() {
    document.getElementById('carte-container').style.display = 'block';
    setTimeout(() => {
        if (window.myMap) {
            window.myMap.invalidateSize();
        }
    }, 200);
});

