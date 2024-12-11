<?php
session_start();
include 'API.php';

if (!isset($_SESSION['user'])) {
    header("Location: Connexion.php");
    exit();
}

$user = $_SESSION['user'];
$conn = connect_db();
$notification = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_reservation'])) {
    $id_reservation = $_POST['id_reservation'];
    $sql = "SELECT * FROM annulation WHERE id_reservation='$id_reservation'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $notification = 'Vous avez déjà envoyé une demande d\'annulation pour cette réservation.';
    } else {
        $sql = "INSERT INTO annulation (id_reservation) VALUES ('$id_reservation')";
        if ($conn->query($sql) === TRUE) {
            $notification = 'Votre demande d\'annulation a été envoyée.';
        } else {
            $notification = 'Erreur lors de l\'envoi de la demande d\'annulation.';
        }
    }
}

$sql = "SELECT r.*, c.type_chambre FROM reservation r JOIN chambre c ON r.id_chambre = c.id_chambre WHERE r.id_client = '{$user['id_client']}'";
$result = $conn->query($sql);
$reservations = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-900 text-white">
    <?php include 'Header.php'; ?>
    <div class="container mx-auto mt-16">
        <h1 class="text-3xl font-bold mb-4">Mes Réservations</h1>
        <?php if ($notification): ?>
            <div class="bg-blue-500 text-white p-4 rounded mb-4">
                <?php echo $notification; ?>
            </div>
        <?php endif; ?>
        <table class="min-w-full divide-y divide-gray-200 bg-gray-800">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Type de Chambre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date Début</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date Fin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Options</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Prix Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo $reservation['type_chambre']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo $reservation['date_debut_sejour']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo $reservation['date_fin_sejour']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo $reservation['options']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo $reservation['prix_total']; ?>€</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form method="POST" action="">
                                <input type="hidden" name="id_reservation" value="<?php echo $reservation['id_reservation']; ?>">
                                <button type="submit" name="cancel_reservation" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Annuler</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>