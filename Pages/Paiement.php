
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "HotelPaie";

    // Créer la connexion
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insertion des données de paiement
    $id_reservation = $_POST['id_reservation'];
    $mode_paiement = $_POST['mode_paiement'];
    $total_prix = $_POST['total_prix'];

    $paiement_sql = "INSERT INTO Paiement (mode_paiement, paiement_statut, Payment_Date, total_prix, id_reservation) VALUES ('$mode_paiement', 1, NOW(), $total_prix, $id_reservation)";
    if ($conn->query($paiement_sql) === TRUE) {
        echo "Paiement réussi!";
    } else {
        echo "Erreur: " . $paiement_sql . "<br>" . $conn->error;
    }

    // Fermeture de la connexion
    $conn->close();
} else {
    $id_reservation = $_GET['id_reservation'];
    echo "<h2>Paiement</h2>";
    echo "<form action='Paiement.php' method='post'>";
    echo "<input type='hidden' name='id_reservation' value='$id_reservation'>";
    echo "<label for='mode_paiement'>Mode de paiement:</label>";
    echo "<input type='text' id='mode_paiement' name='mode_paiement' required><br><br>";
    echo "<label for='total_prix'>Total à payer:</label>";
    echo "<input type='number' id='total_prix' name='total_prix' required><br><br>";
    echo "<input type='submit' value='Payer'>";
    echo "</form>";
}
?>