document.addEventListener('DOMContentLoaded', () => {
    const inputRue = document.getElementById('lieu_rue');

    const suggestionsBox = document.getElementById('rueSuggestions');
    inputRue.addEventListener('input', async (e) => {
    console.log('aaaaaaaaaa');
        const query = e.target.value.trim();
        if (query.length < 3) {
            suggestionsBox.innerHTML = '';
            return;
        }

        const apiUrl = inputRue.dataset.autocompleteUrl + '?q=' + encodeURIComponent(query) + '&limit=5';
        const response = await fetch(apiUrl);
        const data = await response.json();

        suggestionsBox.innerHTML = '';
        data.features.forEach(feature => {
            const li = document.createElement('li');
            li.classList.add('list-group-item', 'list-group-item-action');
            li.textContent = feature.properties.label;

            li.addEventListener('click', () => {
                inputRue.value = feature.properties.name;
                document.getElementById('lieu_latitude').value = feature.geometry.coordinates[1];
                document.getElementById('lieu_longitude').value = feature.geometry.coordinates[0];
                suggestionsBox.innerHTML = '';
            });

            suggestionsBox.appendChild(li);
        });
    });
});
