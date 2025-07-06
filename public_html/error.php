<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Errore</title>
    <style>
        /* Stili personalizzati per la pagina di errore */
        body {
            font-family: 'Arial', sans-serif;
            color: var(--default-color);
            background-color: var(--background-color);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .error-container {
            text-align: center;
            padding: 50px;
            background-color: var(--surface-color);
            border: 2px solid color-mix(in srgb, var(--default-color), transparent 20%);
            border-radius: 10px;
        }

        h1 {
            font-size: 72px;
            color: var(--accent-color);
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            color: color-mix(in srgb, var(--default-color), transparent 30%);
        }

        .btn-home {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: var(--accent-color);
            color: var(--contrast-color);
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-home:hover {
            background-color: color-mix(in srgb, var(--accent-color), transparent 85%);
        }

        svg {
            width: 100px;
            height: 100px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <div class="error-container aos-init aos-animate" data-aos="fade-up">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
            class="bi bi-emoji-frown-fill" viewBox="0 0 16 16">
            <path
                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m-2.715 5.933a.5.5 0 0 1-.183-.683A4.5 4.5 0 0 1 8 9.5a4.5 4.5 0 0 1 3.898 2.25.5.5 0 0 1-.866.5A3.5 3.5 0 0 0 8 10.5a3.5 3.5 0 0 0-3.032 1.75.5.5 0 0 1-.683.183M10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8" />
        </svg>
        <h1>Oops!</h1>
        <p> <?= htmlspecialchars($_GET['error'] ?? "Qualcosa è andato storto. Riprova più tardi.") ?> </p>
        <a href="index.php" class="btn-home">Prova a tornare alla Home</a>
    </div>

</body>

</html>