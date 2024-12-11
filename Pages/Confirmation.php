<?php
session_start(); // Start the session

include '../Pages/Header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
    <link rel="stylesheet" href="../Styles/style.css">
    <link rel="stylesheet" href="../Styles/confirmation.css">
</head>
<body>
    <div class="cadran">
        <h1> Votre réservation a été confirmée.</h1>
        <h2> Merci !! </h2>
        <p> Votre réservation a bien été prise en compte. </p>
        <p> Vous pouvez consulter cette réservation à tout moment dans l'onglet <strong><i>"Mes Réservations"</i></strong>. </p>
        <p> Nous vous remercions et avons hâte de vous accueillir dans notre hôtel. </p>
        <button> <a href="../index.php">Retourner à l'accueil </a> </button>
        <button> <a href="../Pages/Profil.php"> Voir mes réservations </a></button>
    </div>
</body>
</html>