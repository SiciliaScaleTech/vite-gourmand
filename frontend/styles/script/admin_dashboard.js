document.addEventListener("DOMContentLoaded", () => {
    let monGraphique = null;

    // 1. Initialisation de Chart.js
    const ctx = document.getElementById('chartMenus');
    if (ctx && typeof labelsMenus !== 'undefined' && typeof donneesVentes !== 'undefined') {
        monGraphique = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsMenus,
                datasets: [{
                    label: 'Nombre de menus vendus',
                    data: donneesVentes,
                    backgroundColor: '#198754',
                    borderColor: '#146c43',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    // 2. Interception du formulaire de filtrage (Ajax)
    const formFiltre = document.getElementById('formFiltre');
    if (formFiltre) {
        console.log("L'espion JS : Le formulaire a bien été trouvé dans la page !");

        formFiltre.addEventListener('submit', (e) => {
            e.preventDefault(); // Empêche le rechargement de la page

            const dateDebut = document.getElementById('date_debut').value;
            const dateFin = document.getElementById('date_fin').value;

            console.log("Clic détecté ! Dates envoyées :", { dateDebut, dateFin });

            fetch('sync_passerelle.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ date_debut: dateDebut, date_fin: dateFin })
            })
            .then(response => response.json())
            .then(res => {
                console.log("Réponse brute reçue de la passerelle :", res);

                if (res.status === 'success') {
                    // Mise à jour du Chiffre d'Affaires textuel
                    document.getElementById('affichage-ca').innerText = res.ca;

                    // Mise à jour du graphique
                    if (monGraphique) {
                        monGraphique.data.labels = res.labels;
                        monGraphique.data.datasets[0].data = res.donnees;
                        monGraphique.update(); // Redessine le graphique
                    }
                } else {
                    console.error("Erreur retournée par le PHP:", res.message);
                }
            })
            .catch(err => console.error("Erreur Fetch mécanique:", err));
        });
    } else {
        console.log("L'espion JS : Formulaire INTROUVABLE.");
    }
}); 