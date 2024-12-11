<?php
session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "L'adresse mail est invalide.";
    }
    if (empty($password)) {
        $errors['password'] = "Le mot de passe est requis.";
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

        $sql = "SELECT * FROM client WHERE adresse_mail = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['mot_de_passe'])) {
                $_SESSION['user'] = $user;
                header("Location: ../index.php");
                exit();
            } else {
                $errors['general'] = "Email ou mot de passe invalide.";
            }
        } else {
            $errors['general'] = "Email ou mot de passe invalide.";
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
    <title>Connexion</title>
</head>
<body class="h-full">
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <a href="../index.php">
      <img class="mx-auto h-20" src="../Ressources/logo.png" alt="logo" style="width: 40%;">
    </a>
    <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Connexion</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <form class="space-y-6" action="" method="POST">
      <div>
        <label for="email" class="block text-sm/6 font-medium text-gray-900">Adresse mail</label>
        <div class="mt-2">
          <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
          <?php if (isset($errors['email'])): ?>
            <p class="text-red-500 text-sm mt-2"><?php echo $errors['email']; ?></p>
          <?php endif; ?>
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between">
          <label for="password" class="block text-sm/6 font-medium text-gray-900">Mot de passe</label>
         
        </div>
        <div class="mt-2">
          <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
          <?php if (isset($errors['password'])): ?>
            <p class="text-red-500 text-sm mt-2"><?php echo $errors['password']; ?></p>
          <?php endif; ?>
        </div>
      </div>

      <?php if (isset($errors['general'])): ?>
        <p class="text-red-500 text-sm mt-2" style="color: red; font-weight: 700;"><?php echo $errors['general']; ?></p>
      <?php endif; ?>

      <div>
        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign in</button>
      </div>
    </form>

    <p class="mt-10 text-center text-sm/6 text-gray-500">
      Tu n'a pas de compte inscris toi ->
      <a href="./Inscription.php" class="font-semibold text-indigo-600 hover:text-indigo-500">Inscription</a>
    </p>
  </div>
</div>
</body>
</html>