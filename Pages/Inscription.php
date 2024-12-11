<?php
session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $num_tel = $_POST['num_tel'];
    $password = $_POST['password'];

    // Validation
    if (empty($nom)) {
        $errors['nom'] = "Le nom est requis.";
    }
    if (empty($prenom)) {
        $errors['prenom'] = "Le prénom est requis.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "L'adresse mail est invalide.";
    }
    if (!preg_match('/^[0-9]{10}$/', $num_tel)) {
        $errors['num_tel'] = "Le numéro de téléphone est invalide.";
    }

    if (empty($errors)) {
        // Connexion à la base de données
        $servername = "localhost";
        $username = "root";
        $password_db = "";
        $dbname = "hotel"; // Updated database name

        $conn = new mysqli($servername, $username, $password_db, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Vérifier si l'utilisateur existe déjà
        $sql = "SELECT * FROM client WHERE adresse_mail = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors['general'] = "Un compte avec cette adresse mail existe déjà.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO client (nom, prenom, adresse_mail, num_tel, mot_de_passe) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $nom, $prenom, $email, $num_tel, $hashed_password);
            if ($stmt->execute()) {
                $_SESSION['user'] = [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'adresse_mail' => $email,
                    'num_tel' => $num_tel,
                    'mot_de_passe' => $hashed_password
                ];
                header("Location: ../index.php");
                exit();
            } else {
                $errors['general'] = "Erreur lors de l'inscription.";
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/style.css">
    <title>Inscription</title>
</head>
<body class="h-full">
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <a href="../index.php">
      <img class="mx-auto h-12" src="../Ressources/logo.png" alt="Your Company" style="width: 40%;">
    </a>
    <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900" style="font-size: 2rem;">Inscription</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm" style="max-width: 30rem;">
    <form class="space-y-6" action="" method="POST">
      <div class="flex space-x-4">
        <div class="flex-1">
          <label for="nom" class="block text-sm/6 font-medium text-gray-900">Nom</label>
          <div class="mt-2">
            <input id="nom" name="nom" type="text" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
            <?php if (isset($errors['nom'])): ?>
              <p class="text-red-500 text-sm" style="color: red; font-weight: 700;"><?php echo $errors['nom']; ?></p>
            <?php endif; ?>
          </div>
        </div>
        <div class="flex-1">
          <label for="prenom" class="block text-sm/6 font-medium text-gray-900">Prénom</label>
          <div class="mt-2">
            <input id="prenom" name="prenom" type="text" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
            <?php if (isset($errors['prenom'])): ?>
              <p class="text-red-500 text-sm" style="color: red; font-weight: 700;"><?php echo $errors['prenom']; ?></p>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="flex space-x-4">
        <div class="flex-1">
          <label for="email" class="block text-sm/6 font-medium text-gray-900">Adresse mail</label>
          <div class="mt-2">
            <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
            <?php if (isset($errors['email'])): ?>
              <p class="text-red-500 text-sm" style="color: red; font-weight: 700;"><?php echo $errors['email']; ?></p>
            <?php endif; ?>
          </div>
        </div>
        <div class="flex-1">
          <label for="num_tel" class="block text-sm/6 font-medium text-gray-900">Numéro de téléphone</label>
          <div class="mt-2">
            <input id="num_tel" name="num_tel" type="tel" pattern="[0-9]{10}" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
            <?php if (isset($errors['num_tel'])): ?>
              <p class="text-red-500 text-sm" style="color: red; font-weight: 700;"><?php echo $errors['num_tel']; ?></p>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div>
        <label for="password" class="block text-sm/6 font-medium text-gray-900">Mot de passe</label>
        <div class="mt-2">
          <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
        </div>
      </div>

      <?php if (isset($errors['general'])): ?>
        <p class="text-red-500 text-sm" style="color: red; font-weight: 700;"><?php echo $errors['general']; ?></p>
      <?php endif; ?>

      <div>
        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">S'inscrire</button>
      </div>
    </form>

    <p class="mt-10 text-center text-sm/6 text-gray-500">
      Vous avez déjà un compte ?
      <a href="./Connexion.php" class="font-semibold text-indigo-600 hover:text-indigo-500">Connexion</a>
    </p>
  </div>
</div>
</body>
</html>