<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Paie</title>
    <link rel="stylesheet" href="./Styles/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .fixed-size {
            width: 400px;
            height: 250px;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-gray-900 text-white pt-16">
    <?php
    include 'Pages/Header.php';

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hotel"; // Updated database name

    // Créer la connexion
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Connexion à la base de données Azure
    try {
        $connAzure = new PDO("sqlsrv:server = tcp:hotelpaie.database.windows.net,1433; Database = hotelpaiedb", "chunchunmaru", "Flying9lwa21-");
        $connAzure->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
        print("Error connecting to SQL Server.");
        die(print_r($e));
    }

    // SQL Server Extension Sample Code:
    $connectionInfo = array("UID" => "chunchunmaru", "pwd" => "Flying9lwa21-", "Database" => "hotelpaiedb", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
    $serverName = "tcp:hotelpaie.database.windows.net,1433";
    $connAzure = sqlsrv_connect($serverName, $connectionInfo);

    // Fetch room details
    $sql = "SELECT type_chambre, prix_par_nuit, REPLACE(url_image, '../Ressources/', './Ressources/') as url_image FROM chambre GROUP BY type_chambre LIMIT 3";
    $result = $conn->query($sql);
    ?>

<div class="bg-gray-900">
  <div class="relative isolate px-6 pt-14 lg:px-8" style="background-image: url('https://www.luxinmo.com/images/blogs/hoteles-alicante.webp'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-black opacity-70"></div>
    <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
      <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
    </div>
    <div class="relative mx-auto max-w-2xl py-32 sm:py-30 lg:py-30">
      <div class="text-center">
        <img src="./Ressources/logo.png" alt="Hotel Image" class="mx-auto mb-8 w-32 h-32 object-cover">
        <h1 class="text-balance text-5xl font-semibold tracking-tight text-white sm:text-7xl">Bienvenue à</br> Hotel Paie</h1>
        <p class="mt-8 text-pretty text-lg font-medium text-gray-200 sm:text-xl/8">Profitez de nos offres exclusives et réservez votre séjour dès maintenant.</p>
        <div class="mt-10 flex items-center justify-center gap-x-6">
          <a href="/Hotel-Paie/Pages/Chambres.php" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-lg font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Découvrer nos Chambres !</a>
        </div>
      </div>
    </div>
    <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
      <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
    </div>
  </div>
</div>

<div class="bg-gray-100">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl py-16 sm:py-24 lg:max-w-none lg:py-20">
      <h2 class="text-center text-4xl font-bold text-gray-900 mb-20">CHAMBRES À LA UNE</h2>
      <div class="mt-6 space-y-12 lg:grid lg:grid-cols-3 lg:gap-x-6 lg:space-y-0">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $url_image = $row["url_image"] ? $row["url_image"] : 'images/default.webp';
                echo '<div class="group relative">';
                echo '<img src="' . $url_image . '" alt="image_chambre' . $row["type_chambre"] . '" class="fixed-size w-full rounded-lg bg-white object-cover group-hover:opacity-75">';
                echo '<h3 class="mt-6 text-lg font-semibold text-gray-700">';
                echo '<a href="/Hotel-Paie/Pages/Chambres.php">';
                echo '<span class="absolute inset-0"></span>';
                echo $row["type_chambre"];
                echo '</a>';
                echo '</h3>';
                echo '<p class="text-xl font-bold text-gray-900">' . $row["prix_par_nuit"] . ' € par nuit</p>';
                echo '</div>';
            }
        } else {
            echo "0 results";
        }
        $conn->close();
        ?>
      </div>
      <div class="mt-10 flex items-center justify-center gap-x-6">
          <a href="/Hotel-Paie/Pages/Chambres.php" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-lg font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Réserver votre Chambre !</a>
        </div>
    </div>
  </div>
</div>

</body>
</html>