<?php
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>

    <style>
    body {
        font-family: sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
    }

    h1 {
        margin-bottom: 1.5rem;
        color: var(--primary-color);
    }

    .card ul {
        list-style: none;
        padding: 0;
    }

    .card ul li {
        margin-bottom: 0.5rem;
    }

    .card ul li a {
        color: var(--secondary-color);
        text-decoration: none;
        font-weight: 500;
    }

    .card ul li a:hover {
        text-decoration: underline;
    }

    .card h4 {
        margin-bottom: 0.5rem;
        color: var(--primary-color);
    }
    </style>
</head>

<body>
    <section>
        <div>
            <h1>fracturation - Supermarché</h1>
            <p>
                Scanner le code-barres :
            </p>
            <input type="text">
            <input type="submit" value="ajouter">
        </div>
        <div>
            <table>

            </table>
        </div>
    </section>
</body>

</html>