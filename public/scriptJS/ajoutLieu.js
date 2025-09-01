document.getElementById('addLieuForm').addEventListener('submit', function(e) {

    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form)

    fetch(form.action, {
        method:'POST',
        body: formData,
    })
        .then(response=>response.json())
        .then(data => {
            if (data.errors) {
                alert(data.errors.join('\n'));
                return;
            }
            const select = document.getElementById('sortie_lieu')
            const option = new Option(data.nom, data.id, true, true)
            select.add(option)

            const modalEl = document.getElementById('addLieuModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            form.reset();

        });


});
