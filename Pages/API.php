<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function connect_db() {
    $conn = new mysqli("localhost", "root", "", "hotel");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function delete_client($id_client) {
    $conn = connect_db();
    $sql = "DELETE FROM client WHERE id_client='$id_client'";
    $conn->query($sql);
    $conn->close();
}

function edit_client($id_client) {
    $conn = connect_db();
    $sql = "SELECT * FROM client WHERE id_client='$id_client'";
    $result = $conn->query($sql);
    $editClient = null;
    if ($result->num_rows > 0) {
        $editClient = $result->fetch_assoc();
    }
    $conn->close();
    return $editClient;
}

function update_client($id_client, $nom, $prenom, $num_tel, $adresse_mail, $mot_de_passe = null) {
    $conn = connect_db();
    $sql = "UPDATE client SET nom='$nom', prenom='$prenom', num_tel='$num_tel', adresse_mail='$adresse_mail'";
    if ($mot_de_passe) {
        $mot_de_passe = password_hash($mot_de_passe, PASSWORD_BCRYPT);
        $sql .= ", mot_de_passe='$mot_de_passe'";
    }
    $sql .= " WHERE id_client='$id_client'";
    $conn->query($sql);
    $conn->close();
}

function add_client($nom, $prenom, $num_tel, $adresse_mail, $mot_de_passe) {
    $conn = connect_db();
    $mot_de_passe = password_hash($mot_de_passe, PASSWORD_BCRYPT);
    $sql = "INSERT INTO client (nom, prenom, num_tel, adresse_mail, mot_de_passe) VALUES ('$nom', '$prenom', '$num_tel', '$adresse_mail', '$mot_de_passe')";
    $conn->query($sql);
    $conn->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

function cancel_reservation($id_reservation) {
    $conn = connect_db();
    $sql = "DELETE FROM reservation WHERE id_reservation='$id_reservation'";
    $conn->query($sql);
    $conn->close();
}

function confirm_cancel_reservation($id_reservation) {
    $conn = connect_db();
    $sql = "DELETE FROM reservation WHERE id_reservation='$id_reservation'";
    $conn->query($sql);
    $sql = "DELETE FROM annulation WHERE id_reservation='$id_reservation'";
    $conn->query($sql);
    $conn->close();
}

function revoke_cancel_reservation($id_reservation) {
    $conn = connect_db();
    $sql = "DELETE FROM annulation WHERE id_reservation='$id_reservation'";
    $conn->query($sql);
    $conn->close();
}

function edit_reservation($id_reservation) {
    $conn = connect_db();
    $sql = "SELECT * FROM reservation WHERE id_reservation='$id_reservation'";
    $result = $conn->query($sql);
    $editReservation = null;
    if ($result->num_rows > 0) {
        $editReservation = $result->fetch_assoc();
    }
    $conn->close();
    return $editReservation;
}

function update_reservation($id_reservation, $nombre_voyageurs, $date_reservation, $date_debut_sejour, $date_fin_sejour, $options, $prix_total) {
    $conn = connect_db();
    $options = json_encode($options);

    // Fetch the price per night for the room
    $sql = "SELECT prix_par_nuit FROM chambre WHERE id_chambre = (SELECT id_chambre FROM reservation WHERE id_reservation = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_reservation);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $prixParNuit = $row['prix_par_nuit'];
    $stmt->close();

    $prix_total = calculate_total_price($nombre_voyageurs, $date_debut_sejour, $date_fin_sejour, json_decode($options, true), $prixParNuit);

    $sql = "UPDATE reservation SET nombre_voyageurs='$nombre_voyageurs', date_reservation='$date_reservation', date_debut_sejour='$date_debut_sejour', date_fin_sejour='$date_fin_sejour', options='$options', prix_total='$prix_total' WHERE id_reservation='$id_reservation'";
    $conn->query($sql);
    $conn->close();
}

function calculate_total_price($nombre_voyageurs, $date_debut_sejour, $date_fin_sejour, $options, $prixParNuit) {
    $tarifsOptions = [
        'petit_dejeuner' => 10,
        'parking' => 15,
        'spa' => 20
    ];
    $fraisSejourParNuit = 6;

    $nbNuits = (strtotime($date_fin_sejour) - strtotime($date_debut_sejour)) / (60 * 60 * 24);
    $totalChambre = $nbNuits * $prixParNuit;
    $totalOptions = 0;

    if (is_array($options)) {
        foreach ($options as $option) {
            $optionPrix = $tarifsOptions[$option] ?? 0;
            $optionTotal = in_array($option, ['petit_dejeuner', 'spa']) ? $optionPrix * $nbNuits * $nombre_voyageurs : $optionPrix * $nbNuits;
            $totalOptions += $optionTotal;
        }
    }

    $fraisSejour = $fraisSejourParNuit * $nbNuits;
    return $totalChambre + $totalOptions + $fraisSejour;
}

function create_reservation($data) {
    $conn = connect_db();
    $user = $_SESSION['user'] ?? null;

    if (!$user) {
        $conn->close();
        return;
    }

    // Debugging information
    error_log("Received data: " . print_r($data, true));

    if (isset($data['options'], $data['checkin'], $data['checkout'], $data['guests'])) {
        $options = $data['options'];
        $date_debut_sejour = $data['checkin'];
        $date_fin_sejour = $data['checkout'];
        $nombre_voyageurs = (int)$data['guests'];
    } else {
        $conn->close();
        return;
    }

    $date_reservation = date('Y-m-d H:i:s');
    $id_client = $user['id_client'];
    $id_chambre = $_GET['id_chambre'];

    // Fetch the price per night for the room
    $sql = "SELECT prix_par_nuit FROM chambre WHERE id_chambre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_chambre);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $prixParNuit = $row['prix_par_nuit'];
    $stmt->close();

    // Calculate the total price
    $prix_total = calculate_total_price($nombre_voyageurs, $date_debut_sejour, $date_fin_sejour, $options, $prixParNuit);

    // Debugging information
    error_log("Creating reservation with data: " . print_r($data, true));
    error_log("User ID: " . $id_client);
    error_log("Room ID: " . $id_chambre);

    $sql = "INSERT INTO reservation (nombre_voyageurs, date_reservation, date_debut_sejour, date_fin_sejour, options, prix_total, id_client, id_chambre) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "issssdii", 
        $nombre_voyageurs, 
        $date_reservation, 
        $date_debut_sejour, 
        $date_fin_sejour, 
        json_encode($options), 
        $prix_total, 
        $id_client, 
        $id_chambre
    );

    $stmt->execute();
    $stmt->close();
    $conn->close();
}

$editClient = null;
$editReservation = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($_POST['delete'])) {
        delete_client($_POST['id_client']);
        
    } elseif (isset($_POST['edit'])) {
        $editClient = edit_client($_POST['id_client']);

    } elseif (isset($_POST['update'])) {
        update_client($_POST['id_client'], $_POST['nom'], $_POST['prenom'], $_POST['num_tel'], $_POST['adresse_mail'], $_POST['mot_de_passe']);

    } elseif (isset($_POST['add'])) {
        add_client($_POST['nom'], $_POST['prenom'], $_POST['num_tel'], $_POST['adresse_mail'], $_POST['mot_de_passe']);

    } elseif (isset($_POST['cancel'])) {
        cancel_reservation($_POST['id_reservation']);

    } elseif (isset($_POST['confirm_cancel'])) {
        confirm_cancel_reservation($_POST['id_reservation']);

    } elseif (isset($_POST['revoke_cancel'])) {
        revoke_cancel_reservation($_POST['id_reservation']);

    } elseif (isset($_POST['edit_reservation'])) {
        $editReservation = edit_reservation($_POST['id_reservation']);

    } elseif (isset($_POST['update_reservation'])) {
        update_reservation($_POST['id_reservation'], $_POST['nombre_voyageurs'], $_POST['date_reservation'], $_POST['date_debut_sejour'], $_POST['date_fin_sejour'], $_POST['options'], $_POST['prix_total']);
    
    } elseif (isset($input['options'])) {
        create_reservation($input);
    }
}
?>