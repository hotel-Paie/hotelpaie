<?php
session_start(); // Start the session

include '../Pages/Header.php';

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupération de l'ID de la chambre sélectionnée via $_GET
$id_chambre = $_GET['id_chambre'] ?? null;

// Récupération des dates transmises via $_GET
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$voyageurs = $_GET['guests'] ?? 1;

// Récupération des informations de la chambre
$chambre = null;
if ($id_chambre) {
    $sql = "SELECT * FROM chambre WHERE id_chambre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_chambre);
    $stmt->execute();
    $result = $stmt->get_result();
    $chambre = $result->fetch_assoc();
    $stmt->close();
}

// Vérification si l'utilisateur est connecté
$user = $_SESSION['user'] ?? null;

// Gestion de la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['prix_total'], $_POST['date_arrivee'], $_POST['date_depart'], $_POST['voyageurs'])) {
        $options = isset($_POST['options']) ? json_encode($_POST['options']) : json_encode([]);
        $prix_total = (float)$_POST['prix_total'];
        $date_debut_sejour = $_POST['date_arrivee'];
        $date_fin_sejour = $_POST['date_depart'];
        $nombre_voyageurs = (int)$_POST['voyageurs'];
    } else {
        echo json_encode(['success' => false, 'message' => 'Données manquantes']);
        exit;
    }

    // Reste des données
    $date_reservation = date('Y-m-d H:i:s');
    $id_client = $user['id_client'];
    $id_chambre = $_POST['id_chambre'];

    // Insérer la réservation
    $sql = "INSERT INTO reservation (nombre_voyageurs, date_reservation, date_debut_sejour, date_fin_sejour, options, prix_total, id_client, id_chambre) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "issssdii", 
        $nombre_voyageurs, 
        $date_reservation, 
        $date_debut_sejour, 
        $date_fin_sejour, 
        $options, 
        $prix_total, 
        $id_client, 
        $id_chambre
    );

    if ($stmt->execute()) {
        echo "<script>
                window.location.href = '../Pages/Confirmation.php';
              </script>";
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
    exit;
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation</title>
    <link rel="stylesheet" href="../Styles/style.css">
    <link rel="stylesheet" href="../Styles/reservation.css">
</head>
<body>
    <section class="reservation-page">
        <!-- Rappel de la chambre -->
        <div class="room-details">
            <?php if ($chambre): ?>
                <h1> Chambre sélectionnée </h1>
                <?php echo"<img src='" . htmlspecialchars($chambre['url_image']) . "' alt='Chambre'>"; ?>
                <h2>Chambre <?= htmlspecialchars($chambre['type_chambre']) ?></h2>
                <p><?= htmlspecialchars($chambre['description']) ?></p>
                <p><u>Capacité maximale:</u> <?= htmlspecialchars($chambre['capacite_max']) ?> personne(s)</p>
                <p2><?= htmlspecialchars($chambre['prix_par_nuit']) ?> € /nuit</p2>
            <?php else: ?>
                <p>Erreur : Aucune chambre sélectionnée.</p>
            <?php endif; ?>
        </div>

        <!-- Section formulaire et récapitulatif -->
        <div class="reservation-container">
            <!-- Colonne de gauche : Formulaire -->
            <div class="reservation-form">
                <h3>Formulaire de réservation</h3>
                <?php if (!$user): ?>
                    <p>Veuillez vous connecter pour continuer votre réservation.</p>
                    <a href="../Pages/Connexion.php" class="button">Se connecter</a>
                <?php else: ?>
                    <form method="post">
                        <input type="hidden" name="id_chambre" value="<?= htmlspecialchars($id_chambre) ?>">
                        
                        <h4>1 - Informations personnelles</h4>
                        <label>Prénom : <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" required></label>
                        <label>Nom : <input type="text" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required></label>
                        <label>Email : <input type="email" name="email" value="<?= htmlspecialchars($user['adresse_mail'] ?? '') ?>" required></label>
                        <label>Téléphone : <input type="text" name="telephone" value="<?= htmlspecialchars($user['num_tel'] ?? '') ?>" required></label>

                        <h4>2 - Détails du séjour</h4>
                        <label>Date d'arrivée : <input type="date" name="date_arrivee" value="<?= htmlspecialchars($checkin) ?>" required></label>
                        <label>Date de départ : <input type="date" name="date_depart" value="<?= htmlspecialchars($checkout) ?>" required></label>
                        <label>Nombre de voyageurs : <input type="number" name="voyageurs" min="1" value="<?= htmlspecialchars($voyageurs) ?>" required></label>

                        <h4>3 - Options</h4>
                        <div class="options">
                            <label><input type="checkbox" name="options[]" value="petit_dejeuner" <?= isset($_POST['options']) && in_array('petit_dejeuner', $_POST['options']) ? 'checked' : '' ?>> Petit déjeuner (+10€/personne/nuit)</label>
                            <label><input type="checkbox" name="options[]" value="parking" <?= isset($_POST['options']) && in_array('parking', $_POST['options']) ? 'checked' : '' ?>> Parking (+15€)</label>
                            <label><input type="checkbox" name="options[]" value="spa" <?= isset($_POST['options']) && in_array('spa', $_POST['options']) ? 'checked' : '' ?>> Spa (+20€/personne/nuit)</label>
                        </div>
                        <input type="hidden" name="prix_total" id="prix_total">
                        <button type="button" id="confirm-button-left">Confirmer</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Colonne de droite : Récapitulatif -->
            <div class="summary"
                data-prix-par-nuit="<?= htmlspecialchars($chambre['prix_par_nuit']) ?>" 
                data-duree-sejour="<?= htmlspecialchars($interval->days ?? 1) ?>" 
                data-voyageurs="<?= htmlspecialchars($_POST['voyageurs'] ?? 1) ?>" 
                data-options="<?= htmlspecialchars(json_encode($options ?? [])) ?>">
                <h3>Récapitulatif de la commande</h3>
            </div>
        </div>
    </section>

    <section class="pop-ups">
        <div id="confirmation-modal" class="modal hidden">
        <div class="modal-content">
            <p>Souhaitez-vous vraiment confirmer la réservation ?</p>
            <div class="modal-actions">
                <button id="modal-no">Non</button>
                <button id="modal-yes">Oui</button>
            </div>
        </div>
    </div>

    <div id="final-message-modal" class="modal hidden">
        <div class="modal-content">
            <p id="final-message">Votre message ici.</p>
            <button id="home-final-message">Accueil</button>
        </div>
    </div>
    </section>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const summaryContainer = document.querySelector(".summary");

            // Récupération des données initiales
            const prixParNuit = parseFloat(summaryContainer.dataset.prixParNuit);
            const tarifsOptions = {
                petit_dejeuner: 10, // 10€/personne/nuit
                parking: 15,       // 15€/nuit
                spa: 20            // 20€/personne/nuit
            };
            const fraisSejourParNuit = 6;

            const formInputs = {
                checkin: document.querySelector('input[name="date_arrivee"]'),
                checkout: document.querySelector('input[name="date_depart"]'),
                guests: document.querySelector('input[name="voyageurs"]'),
                options: document.querySelectorAll('input[name="options[]"]')
            };

            // Fonction pour calculer le prix total
            function updateRecap() {
                const checkinDate = new Date(formInputs.checkin.value);
                const checkoutDate = new Date(formInputs.checkout.value);
                const nbNuits = (checkoutDate - checkinDate) / (1000 * 60 * 60 * 24);
                const nbVoyageurs = parseInt(formInputs.guests.value) || 1;

                if (isNaN(nbNuits) || nbNuits <= 0) {
                    summaryContainer.innerHTML = "<p>Veuillez entrer des dates valides.</p>";
                    return;
                }

                // Calcul des coûts
                const totalChambre = nbNuits * prixParNuit;
                let totalOptions = 0;
                let optionsHTML = "<h4>Options:</h4>";

                formInputs.options.forEach(option => {
                    if (option.checked) {
                        const optionPrix = tarifsOptions[option.value] || 0;
                        const optionTotal =
                            option.value === "petit_dejeuner" || option.value === "spa"
                                ? optionPrix * nbNuits * nbVoyageurs
                                : optionPrix * nbNuits;

                        totalOptions += optionTotal;
                        optionsHTML += `<p>• ${option.value.replace("_", " ")}: <i>${optionTotal.toFixed(2)} €</i></p>`;   
                    }
                });

                if (totalOptions === 0) {
                    optionsHTML += "<p>Aucune option sélectionnée.</p>";
                } else {
                    optionsHTML += `<p3><strong>Total options: </strong><i>${totalOptions} €</i></p3></br></br>`;
                }

                const fraisSejour = fraisSejourParNuit * nbNuits;
                const totalFinal = totalChambre + totalOptions + fraisSejour;

                // Mise à jour du récapitulatif
                summaryContainer.innerHTML = `
                    <h3>Récapitulatif de la commande</h3>
                    <h6>Chambre:</h6>
                    <p>• Prix par nuit: <i>${prixParNuit.toFixed(2)} €</i></p>
                    <p>• Nombre de nuits: ${nbNuits}</p>
                    <p3><strong>Prix total chambre:</strong> <i>${totalChambre.toFixed(2)} €</i></p3>
                    ${optionsHTML}
                    <p4><strong>Frais de séjour: </strong><i>${fraisSejour.toFixed(2)} €</i></p4>
                    <p><strong id='de_corpulence_grosse_blue'>Total final: ${totalFinal.toFixed(2)} €</strong></p>
                    <p><em>Dont TVA: ${(totalFinal * 0.1).toFixed(2)} €</em></p>
                `;

                // Update the hidden input with the calculated total price
                document.getElementById('prix_total').value = totalFinal.toFixed(2);
            }

            // Fonction pour valider les données du formulaire
            function validateFormData() {
                const checkinDate = new Date(formInputs.checkin.value);
                const checkoutDate = new Date(formInputs.checkout.value);
                const nbVoyageurs = parseInt(formInputs.guests.value) || 1;

                if (isNaN(checkinDate) || isNaN(checkoutDate) || checkinDate >= checkoutDate) {
                    alert("Veuillez entrer des dates valides.");
                    return false;
                }

                if (nbVoyageurs <= 0) {
                    alert("Veuillez entrer un nombre valide de voyageurs.");
                    return false;
                }

                return true;
            }

            // Fonction pour rediriger vers la page d'accueil
            function redirectToHomePage() {
                window.location.href = "../index.php";
            }

            // Écouteurs d'événements pour mettre à jour le récapitulatif
            formInputs.checkin.addEventListener("change", updateRecap);
            formInputs.checkout.addEventListener("change", updateRecap);
            formInputs.guests.addEventListener("input", updateRecap);
            formInputs.options.forEach(option =>
                option.addEventListener("change", updateRecap)
            );

            // Initialiser le récapitulatif
            updateRecap();

            // Boutons fenêtres pop-up

            // Sélection des éléments
            const confirmButtonLeft = document.getElementById("confirm-button-left");
            const confirmationModal = document.getElementById("confirmation-modal");
            const finalMessageModal = document.getElementById("final-message-modal");
            const modalNo = document.getElementById("modal-no");
            const modalYes = document.getElementById("modal-yes");
            const homeFinalMessage = document.getElementById("home-final-message");
            
            // Afficher le modal de confirmation
            const showConfirmationModal = () => {
                confirmationModal.classList.remove("hidden");
            };
            
            // Cacher le modal de confirmation
            const hideConfirmationModal = () => {
                confirmationModal.classList.add("hidden");
            };
            
            // Afficher le modal final
            const showFinalMessageModal = () => {
                hideConfirmationModal();
                const finalMessage = document.getElementById("final-message");
                finalMessage.textContent = "Votre réservation a été confirmée ! Merci pour votre confiance.";
                finalMessageModal.classList.remove("hidden");
            };
            
            // Fermer le modal final et rediriger vers la page d'accueil
            const goToHomePage = () => {
                redirectToHomePage();
            };
            
            // Ajouter les événements
            confirmButtonLeft?.addEventListener("click", () => {
                console.log("Bouton gauche cliqué !");
                showConfirmationModal();
            });
                
            modalNo?.addEventListener("click", hideConfirmationModal);
            modalYes?.addEventListener("click", () => {
                if (validateFormData()) {
                    document.querySelector('form').submit();
                }
            });
            homeFinalMessage?.addEventListener("click", goToHomePage);

        });
    </script>
</body>
</html> 
