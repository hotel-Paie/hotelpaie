<?php
session_start();
include 'API.php';

$editClient = null;
$editReservation = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        delete_client($_POST['id_client']);
    } elseif (isset($_POST['edit'])) {
        $editClient = edit_client($_POST['id_client']);
    } elseif (isset($_POST['update'])) {
        update_client($_POST['id_client'], $_POST['nom'], $_POST['prenom'], $_POST['num_tel'], $_POST['adresse_mail']);
    } elseif (isset($_POST['add'])) {
        add_client($_POST['nom'], $_POST['prenom'], $_POST['num_tel'], $_POST['adresse_mail'], $_POST['mot_de_passe']);
    } elseif (isset($_POST['cancel'])) {
        cancel_reservation($_POST['id_reservation']);
    } elseif (isset($_POST['edit_reservation'])) {
        $editReservation = edit_reservation($_POST['id_reservation']);
    } elseif (isset($_POST['update_reservation'])) {
        $options = isset($_POST['options']) ? $_POST['options'] : [];
        update_reservation($_POST['id_reservation'], $_POST['nombre_voyageurs'], $_POST['date_reservation'], $_POST['date_debut_sejour'], $_POST['date_fin_sejour'], $options, $_POST['prix_total']);
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <title>Dashboard</title>
    <style>
        .modal {
            display: none; /* Ensure modals are hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body class="h-full">
    <?php include 'Header.php'; ?>
    <div class="min-h-full">
        <header class="bg-white shadow mt-16">
            <div class="mx-auto max-w-full px-4 py-6 sm:px-6 lg:px-8 text-center">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Dashboard</h1>
            </div>
        </header>
        <main>
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">Clients</h2>
                    <button onclick="openModal('addClientModal')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mx-auto block my-4 mb-6">Ajouter Client</button>
                </div>
                <div id="addClientModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('addClientModal')">&times;</span>
                        <form method="POST" action="" class="space-y-6">
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label for="nom" class="block text-sm/6 font-medium text-gray-900">Nom</label>
                                    <div class="mt-2">
                                        <input id="nom" name="nom" type="text" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label for="prenom" class="block text-sm/6 font-medium text-gray-900">Prénom</label>
                                    <div class="mt-2">
                                        <input id="prenom" name="prenom" type="text" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label for="num_tel" class="block text-sm/6 font-medium text-gray-900">Num Tel</label>
                                    <div class="mt-2">
                                        <input id="num_tel" name="num_tel" type="text" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label for="adresse_mail" class="block text-sm/6 font-medium text-gray-900">Adresse Mail</label>
                                    <div class="mt-2">
                                        <input id="adresse_mail" name="adresse_mail" type="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="mot_de_passe" class="block text-sm/6 font-medium text-gray-900">Mot de Passe</label>
                                <div class="mt-2">
                                    <input id="mot_de_passe" name="mot_de_passe" type="password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div>
                                <button type="submit" name="add" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prenom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Num Tel</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adresse Mail</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $conn = new mysqli("localhost", "root", "", "hotel");
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        $sql = "SELECT * FROM client WHERE id_client > 0";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["nom"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["prenom"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["num_tel"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["adresse_mail"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>
                                        <form method='POST' action=''>
                                            <input type='hidden' name='id_client' value='" . $row["id_client"] . "'>
                                            <button type='button' onclick='openEditClientModal(" . json_encode($row) . ")' class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'>Modifier</button>
                                            <button type='submit' name='delete' class='bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded'>Supprimer</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='px-6 py-4 whitespace-nowrap'>No clients found</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
                <div id="editClientModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('editClientModal')">&times;</span>
                        <form method="POST" action="" class="space-y-6">
                            <input type="hidden" id="edit_id_client" name="id_client">
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label for="edit_nom" class="block text-sm/6 font-medium text-gray-900">Nom</label>
                                    <div class="mt-2">
                                        <input id="edit_nom" name="nom" type="text" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label for="edit_prenom" class="block text-sm/6 font-medium text-gray-900">Prénom</label>
                                    <div class="mt-2">
                                        <input id="edit_prenom" name="prenom" type="text" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label for="edit_num_tel" class="block text-sm/6 font-medium text-gray-900">Num Tel</label>
                                    <div class="mt-2">
                                        <input id="edit_num_tel" name="num_tel" type="text" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label for="edit_adresse_mail" class="block text-sm/6 font-medium text-gray-900">Adresse Mail</label>
                                    <div class="mt-2">
                                        <input id="edit_adresse_mail" name="adresse_mail" type="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="edit_mot_de_passe" class="block text-sm/6 font-medium text-gray-900">Mot de Passe</label>
                                <div class="mt-2">
                                    <input id="edit_mot_de_passe" name="mot_de_passe" type="password" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                </div>
                            </div>
                            <div>
                                <button type="submit" name="update" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 mt-8 mb-4">Reservations</h2>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prenom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type de Chambre</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre Voyageurs</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Reservation</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Debut Sejour</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Fin Sejour</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Options</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $conn = new mysqli("localhost", "root", "", "hotel");
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        $sql = "SELECT r.*, c.nom, c.prenom, ch.type_chambre 
                                FROM reservation r 
                                JOIN client c ON r.id_client = c.id_client 
                                JOIN chambre ch ON r.id_chambre = ch.id_chambre";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["nom"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["prenom"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["type_chambre"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["nombre_voyageurs"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["date_reservation"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["date_debut_sejour"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["date_fin_sejour"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["options"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["prix_total"] . "€</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>
                                        <form method='POST' action=''>
                                            <input type='hidden' name='id_reservation' value='" . $row["id_reservation"] . "'>
                                            <button type='button' onclick='openEditReservationModal(" . json_encode($row) . ")' class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'>Modifier</button>
                                            <button type='submit' name='cancel' class='bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded'>Annuler</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10' class='px-6 py-4 whitespace-nowrap'>Pas de réservation trouvée</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
                <div id="editReservationModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('editReservationModal')">&times;</span>
                        <form method="POST" action="" class="space-y-6">
                            <input type="hidden" id="edit_id_reservation" name="id_reservation">
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label for="edit_nombre_voyageurs" class="block text-sm/6 font-medium text-gray-900">Nombre Voyageurs</label>
                                    <div class="mt-2">
                                        <input id="edit_nombre_voyageurs" name="nombre_voyageurs" type="number" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label for="edit_date_reservation" class="block text-sm/6 font-medium text-gray-900">Date Reservation</label>
                                    <div class="mt-2">
                                        <input id="edit_date_reservation" name="date_reservation" type="date" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label for="edit_date_debut_sejour" class="block text-sm/6 font-medium text-gray-900">Date Debut Sejour</label>
                                    <div class="mt-2">
                                        <input id="edit_date_debut_sejour" name="date_debut_sejour" type="date" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label for="edit_date_fin_sejour" class="block text-sm/6 font-medium text-gray-900">Date Fin Sejour</label>
                                    <div class="mt-2">
                                        <input id="edit_date_fin_sejour" name="date_fin_sejour" type="date" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label for="edit_options" class="block text-sm/6 font-medium text-gray-900">Options</label>
                                    <div class="mt-2">
                                        <label><input type="checkbox" id="edit_option_petit_dejeuner" name="options[]" value="petit_dejeuner"> Petit déjeuner (+10€/personne/nuit)</label>
                                        <label><input type="checkbox" id="edit_option_parking" name="options[]" value="parking"> Parking (+15€)</label>
                                        <label><input type="checkbox" id="edit_option_spa" name="options[]" value="spa"> Spa (+20€/personne/nuit)</label>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label for="edit_prix_total" class="block text-sm/6 font-medium text-gray-900">Prix Total</label>
                                    <div class="mt-2">
                                        <input id="edit_prix_total" name="prix_total" type="number" step="0.01" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" name="update_reservation" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 mt-8 mb-4">Demandes d'Annulation</h2>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prénom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type de Chambre</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre Voyageurs</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Réservation</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Fin Séjour</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Options</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $conn = connect_db();
                        $sql = "SELECT a.id_reservation, c.nom, c.prenom, ch.type_chambre, r.nombre_voyageurs, r.date_reservation, r.date_fin_sejour, r.options, r.prix_total 
                                FROM annulation a 
                                JOIN reservation r ON a.id_reservation = r.id_reservation 
                                JOIN client c ON r.id_client = c.id_client 
                                JOIN chambre ch ON r.id_chambre = ch.id_chambre";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["nom"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["prenom"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["type_chambre"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["nombre_voyageurs"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["date_reservation"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["date_fin_sejour"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["options"] . "</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>" . $row["prix_total"] . "€</td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>
                                        <form method='POST' action=''>
                                            <input type='hidden' name='id_reservation' value='" . $row["id_reservation"] . "'>
                                            <button type='submit' name='confirm_cancel' class='bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded'>Confirmer</button>
                                            <button type='submit' name='revoke_cancel' class='bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded'>Révoquer</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9' class='px-6 py-4 whitespace-nowrap'>Pas de demandes d'annulation</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ensure all modals are hidden on page load
            document.querySelectorAll('.modal').forEach(function(modal) {
                modal.style.display = 'none';
            });
        });

        function openModal(modalId) {
            document.getElementById(modalId).style.display = "flex";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        function openEditClientModal(client) {
            document.getElementById('edit_id_client').value = client.id_client;
            document.getElementById('edit_nom').value = client.nom;
            document.getElementById('edit_prenom').value = client.prenom;
            document.getElementById('edit_num_tel').value = client.num_tel;
            document.getElementById('edit_adresse_mail').value = client.adresse_mail;
            openModal('editClientModal');
        }

        function openEditReservationModal(reservation) {
            document.getElementById('edit_id_reservation').value = reservation.id_reservation;
            document.getElementById('edit_nombre_voyageurs').value = reservation.nombre_voyageurs;
            document.getElementById('edit_date_reservation').value = reservation.date_reservation;
            document.getElementById('edit_date_debut_sejour').value = reservation.date_debut_sejour;
            document.getElementById('edit_date_fin_sejour').value = reservation.date_fin_sejour;
            document.getElementById('edit_prix_total').value = reservation.prix_total;

            // Set options checkboxes
            const options = JSON.parse(reservation.options);
            document.getElementById('edit_option_petit_dejeuner').checked = options.includes('petit_dejeuner');
            document.getElementById('edit_option_parking').checked = options.includes('parking');
            document.getElementById('edit_option_spa').checked = options.includes('spa');

            openModal('editReservationModal');
        }
    </script>
</body>
</html>


