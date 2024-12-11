<?php if (session_status() == PHP_SESSION_NONE) {
    session_start();
} ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Paie</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-900 text-white pt-16">
<header class="fixed top-0 left-0 right-0 z-50">
    <nav class="bg-gray-800">
        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
            <div class="relative flex h-16 items-center justify-between">
                <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                    <div class="flex shrink-0 items-center">
                        <img class="h-9 w-auto" src="https://i.imgur.com/7bKtwjb.png" alt="logo">
                    </div>
                    <div class="hidden sm:ml-6 sm:block">
                        <div class="flex space-x-4">
                            <a href="/Hotel-Paie/index.php" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Accueil</a>
                            <a href="/Hotel-Paie/Pages/Chambres.php" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Nos Chambres</a>
                        </div>
                    </div>
                </div>
                <div class="relative ml-3">
                    <div>
                        <?php if (isset($_SESSION['user'])): ?>
                            <?php if (isset($_SESSION['user']['id_client']) && $_SESSION['user']['id_client'] == 0): ?>
                                <a href="/Hotel-Paie/Pages/Dashboard.php" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Admin Dashboard</a>
                            <?php else: ?>
                                <a href="/Hotel-Paie/Pages/Profil.php" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Votre Profil</a>
                            <?php endif; ?>
                            <span class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 font-bold">
                                Bonjour, <?php echo isset($_SESSION['user']['prenom']) ? $_SESSION['user']['prenom'] : ''; ?> 
                                <?php echo isset($_SESSION['user']['nom']) ? $_SESSION['user']['nom'] : ''; ?>
                            </span>
                            <a href="/Hotel-Paie/Pages/logout.php" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Déconnexion</a>
                        <?php else: ?>
                            <a href="/Hotel-Paie/Pages/Connexion.php" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Connexion</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
</body>
</html>