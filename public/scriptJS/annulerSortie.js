document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById('addAnnulerSortieForm');
    if (!form) return; // si le formulaire n'existe pas, on sort

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log("Formulaire d'annulation soumis");

        const formData = new FormData(form);

        fetch(form.action, {
            method:'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.errors) {
                    alert(data.errors.join('\n'));
                    return;
                }

                console.log("Sortie annulée ✅", data);

                const modalEl = document.getElementById('addAnnulerSortieModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                form.reset();
            })
            .catch(err => console.error("Erreur fetch:", err));
    });
});
