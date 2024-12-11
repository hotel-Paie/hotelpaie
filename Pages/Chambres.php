<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservez votre chambre</title>
    <link rel="stylesheet" href="../Styles/chambres.css">
    <link rel="stylesheet" href="../Styles/style.css">
</head>
<body>
  <?php

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

    ?>

    <section class="search-bar">
        <form method="GET" action="chambres.php">
            <label for="checkin">Date d'arrivée :</label>
            <input type="date" id="checkin" name="checkin" value="<?= isset($_GET['checkin']) ? htmlspecialchars($_GET['checkin']) : '' ?>" min="' . date('Y-m-d') . '" required>

            <label for="checkout">Date de départ :</label>
            <input type="date" id="checkout" name="checkout" value="<?= isset($_GET['checkout']) ? htmlspecialchars($_GET['checkout']) : '' ?>" required>

            <label for="guests">Nombre de voyageurs :</label>
            <input type="number" id="guests" name="guests" value="<?= isset($_GET['guests']) ? htmlspecialchars($_GET['guests']) : '' ?>" min="1" value="1" required>

            <button type="submit">Rechercher</button>
            <button type="button" onclick="window.location.href='chambres.php';">Effacer</button>

        </form>
        <script>
        document.addEventListener("DOMContentLoaded", function () {
            const checkin = document.getElementById("checkin");
            const checkout = document.getElementById("checkout");

            // Mise à jour de la date minimale de départ en fonction de la date d'arrivée
            checkin.addEventListener("change", function () {
                const checkinDate = new Date(this.value);
                const minCheckoutDate = new Date(checkinDate);
                minCheckoutDate.setDate(minCheckoutDate.getDate() + 1); // Départ au moins 1 jour après
                checkout.min = minCheckoutDate.toISOString().split("T")[0];
                if (checkout.value && new Date(checkout.value) <= checkinDate) {
                    checkout.value = ""; // Réinitialise si la date est invalide
                }
            });

            // Définit la date d\'aujourd\'hui comme minimum par défaut pour l\'arrivée
            const today = new Date().toISOString().split("T")[0];
            checkin.min = today;
            checkout.min = today;
        });
    </script>
    </section>

    <!-- Message pour les groupes de plus de 4 personnes -->
    <?php if ($message): ?>
        <p class="alert-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <section class="presentation-chambres">
    <div class="container">
        <!-- Cadran de filtres -->
        <aside class="filters">
            <h3>Filtres</h3>
            <form method="GET" action="chambres.php">
                <!-- Réutilisation des critères de recherche -->
                <label for="tri"><u>Trier par:</u> </label>
                <select name="tri" id="tri">
                    <option value="prix_asc" <?= isset($_GET['tri']) && $_GET['tri'] === 'prix_asc' ? 'selected' : '' ?>>Prix croissant</option>
                    <option value="prix_desc" <?= isset($_GET['tri']) && $_GET['tri'] === 'prix_desc' ? 'selected' : '' ?>>Prix décroissant</option>
                </select>

                <label for="type_chambre"><u>Type de chambre:</u> </label>
                <select name="type_chambre" id="type_chambre">
                    <option value="">Tous</option>
                    <option value="simple" <?= isset($_GET['type_chambre']) && $_GET['type_chambre'] === 'simple' ? 'selected' : '' ?>>Simple</option>
                    <option value="double" <?= isset($_GET['type_chambre']) && $_GET['type_chambre'] === 'double' ? 'selected' : '' ?>>Double</option>
                    <option value="twin" <?= isset($_GET['type_chambre']) && $_GET['type_chambre'] === 'twin' ? 'selected' : '' ?>>Twin</option>
                    <option value="deluxe" <?= isset($_GET['type_chambre']) && $_GET['type_chambre'] === 'deluxe' ? 'selected' : '' ?>>Deluxe</option>
                    <option value="famille" <?= isset($_GET['type_chambre']) && $_GET['type_chambre'] === 'famille' ? 'selected' : '' ?>>Famille</option>
                </select>

                <button type="submit">Appliquer les filtres</button>
            </form>
        </aside>

        <!-- Section des chambres -->
        <div class="rooms-display">
            <?php
            // Récupérer les paramètres de recherche existants
            $checkin = $_GET['checkin'] ?? '';
            $checkout = $_GET['checkout'] ?? '';
            $guests = $_GET['guests'] ?? '';
            $tri = $_GET['tri'] ?? '';
            $type_chambre = $_GET['type_chambre'] ?? '';

            // Initialiser le message pour les groupes
            $message = null;
            if ($guests > 4) {
                $message = "Pour les groupes de plus de 4 personnes, veuillez réserver plusieurs chambres.";
            }

            // Afficher un message si aucune recherche n'a été effectuée
            if (empty($checkin) || empty($checkout)) {
                echo "<p id='message_faire_recherche'>Veuillez effectuer une recherche par date pour voir les chambres disponibles.</p>";
            } else {
                // Construire la requête SQL de base
                $sql = "SELECT * FROM chambre WHERE 1";

                // Ajouter les conditions de recherche par date
                if ($checkin && $checkout) {
                    $sql .= "
                        AND id_chambre NOT IN (
                            SELECT id_chambre
                            FROM reservation
                            WHERE (date_debut_sejour < '$checkout' AND date_fin_sejour > '$checkin')
                        )
                    ";
                }

                // Ajouter la condition sur le nombre de voyageurs
                if ($guests) {
                    if ($guests < 5) {
                        $sql .= " AND capacite_max >= $guests";
                    }
                }

                // Ajouter la condition sur le type de chambre
                if ($type_chambre) {
                    $sql .= " AND type_chambre = '$type_chambre'";
                }

                // Ajouter le tri
                if ($tri === 'prix_asc') {
                    $sql .= " ORDER BY prix_par_nuit ASC";
                } elseif ($tri === 'prix_desc') {
                    $sql .= " ORDER BY prix_par_nuit DESC";
                }

                // Exécuter la requête
                $result = $conn->query($sql);

                // Afficher les chambres disponibles
                if ($result->num_rows > 0) {

                    // Si plus de 4 voyageurs, afficher un message
                    if ($guests > 4) {
                        echo "<p class='alert-message'>$message</p>";
                    }

                    // Affichage des chambres disponibles
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='room'>";
                        echo "<img src='" . htmlspecialchars($row['url_image']) . "' alt='Chambre'>";
                        echo "<h2>Chambre " . htmlspecialchars($row['type_chambre']) . "</h2>";
                        echo "<p1>". htmlspecialchars($row['description']) . "</p>";
                        echo "<p2><u>Prix:</u> " . htmlspecialchars($row['prix_par_nuit']) . " € par nuit</p>";
                        echo "<p3><u>Capacité maximale:</u> " . htmlspecialchars($row['capacite_max']) . " personnes</p>";
                        echo "<form action='Reservations.php' method='GET'>";
                        echo "<input type='hidden' name='id_chambre' value='" . htmlspecialchars($row['id_chambre']) . "'>";
                        echo "<input type='hidden' name='url_image' value='" . htmlspecialchars($row['url_image']) . "'>";
                        echo "<input type='hidden' name='type_chambre' value='" . htmlspecialchars($row['type_chambre']) . "'>";
                        echo "<input type='hidden' name='description' value='" . htmlspecialchars($row['description']) . "'>";
                        echo "<input type='hidden' name='prix_par_nuit' value='" . htmlspecialchars($row['prix_par_nuit']) . "'>";
                        echo "<input type='hidden' name='capacite_max' value='" . htmlspecialchars($row['capacite_max']) . "'>";
                        echo "<input type='hidden' name='checkin' value='" . htmlspecialchars($checkin) . "'>";
                        echo "<input type='hidden' name='checkout' value='" . htmlspecialchars($checkout) . "'>";
                        echo "<input type='hidden' name='guests' value='" . htmlspecialchars($guests) . "'>";
                        echo "<button type='submit'>Réserver</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Aucune chambre disponible pour les critères sélectionnés.</p>";
                }
            }

            // Fermeture de la connexion
            $conn->close();
            ?>
        </div>
    </div>
</section>


</body>
</html>

