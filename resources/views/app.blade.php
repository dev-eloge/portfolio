<!DOCTYPE html>
@php($isAdmin = request()->is(config('portfolio.admin_path'), config('portfolio.admin_path').'/*'))
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => $isAdmin])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ \App\Support\Profile::get('name', config('app.name')) }}</title>

        {{-- Icône d'onglet : photo ronde (générée depuis public/images/eloge-face.webp) --}}
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
        <link rel="icon" type="image/png" sizes="48x48" href="/favicon-48.png">
        <link rel="icon" type="image/png" sizes="192x192" href="/icon-192.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

        {{-- Applique le thème choisi avant l'affichage (évite un flash). Jour par défaut. --}}
        <script>
            (function () {
                try {
                    var theme = localStorage.getItem('theme');
                    // Site public : jour par défaut, nuit seulement si choisie avec le bouton.
                    // Admin : toujours en sombre (classe posée par le serveur).
                    if (theme === 'dark') {
                        document.documentElement.classList.add('dark');
                    }
                } catch (e) {}
            })();
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tektur:wght@400..900&family=Poppins:wght@400;500;600;700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
