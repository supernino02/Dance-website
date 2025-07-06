<?php
//PAGINA PHP CHIAMATA DA dal servizio DonloadReceipt.php
//chiamarla in altri modi generea risultati inprevedibili (evitare di chiamarla con PHPComponentPath)

// Funzione per ottenere l'immagine come base64 (per includerla inline nell'HTML)
function getImageBase64($imagePath)
{
    $imageData = file_get_contents($imagePath);
    return base64_encode($imageData);
}

// Percorso all'immagine del logo
$logoPath = "../public_html/MULTIMEDIA/imgs/logo.png"; // Cambia questo percorso con il percorso reale del logo
$logoBase64 = getImageBase64($logoPath);
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ricevuta di Acquisto - FBS LATIN EMPIRE</title>
    <style>
        /* Stili di base */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .receipt-container {
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            color: #b300ff;
            text-align: center;
        }

        h1 {
            font-size: 1.5em;
            margin-bottom: 5px;
        }

        h2 {
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .logo {
            display: block;
            margin: 0 auto 20px;
            max-width: 100px;
        }

        .receipt-header {
            width: 100%;
            margin-bottom: 20px;
        }

        .receipt-header dl {
            display: flex;
            flex-direction: column;
            padding: 0;
            margin: 0;
        }

        .receipt-header div {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            /* Spazio tra le righe */
        }

        .receipt-header dt {
            width: 50%;
            text-align: left;
            font-weight: bold;
        }

        .receipt-header dd {
            width: 50%;
            text-align: right;
            margin: 0;
            /* Assicura che non ci siano margini extra */
        }

        .receipt-products table {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-products th,
        .receipt-products td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ccc;
            font-size: 0.875em;
        }

        .receipt-products th {
            background-color: #f8f9fa;
        }

        .receipt-products td {
            vertical-align: top;
        }

        .total {
            font-size: 1.25em;
            font-weight: bold;
            color: #b300ff;
            text-align: right;
            margin-top: 20px;
        }

        .thank-you {
            text-align: center;
            margin-top: 20px;
            font-size: 1.1em;
            color: #333;
        }

        .contact-info {
            margin-top: 20px;
            text-align: center;
            font-size: 0.875em;
        }

        .contact-info a {
            color: #b300ff;
            text-decoration: none;
        }

        @media print {
            .receipt-container {
                max-width: 100%;
                box-shadow: none;
                margin: 0;
                padding: 0;
            }

            body {
                background-color: #fff;
            }
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <img src="data:image/png;base64,<?php echo $logoBase64; ?>" alt="FBS LATIN EMPIRE Logo" class="logo">
        <h1>FBS LATIN EMPIRE</h1>
        <h2>Scontrino di Acquisto</h2>
        <dl class="receipt-header">
            <div>
                <dt>Cliente</dt>
                <dd><?php echo ucfirst($nome) . ' ' . ucfirst($cognome); ?></dd>
            </div>
            <div>
                <dt>Email</dt>
                <dd><?php echo $user; ?></dd>
            </div>
            <div>
                <dt>Data</dt>
                <dd><?php echo $date_time; ?></dd>
            </div>
        </dl>
        <div class="receipt-products">
            <table>
                <thead>
                    <tr>
                        <th>Prodotto</th>
                        <th>Quantità</th>
                        <th>Prezzo Unitario</th>
                        <th>Prezzo Totale</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['product']['name']; ?></td>
                            <td><?php echo $product['quantity']; ?>x</td>
                            <td><?php echo number_format($product['unitary_price'], 2); ?>€</td>
                            <td><?php echo number_format($product['quantity'] * $product['unitary_price'], 2); ?>€</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="total">Totale: <?php echo number_format($total_price, 2); ?>€</p>
        <div class="thank-you">Grazie per il tuo acquisto!</div>
        <div class="contact-info">
            <p>Contattaci:</p>
            <p>
                <a href="https://wa.me/393338689245?text=Ciao%2C+vorrei+avere+maggiori+informazioni+riguardo+ai+corsi+che+proponete" target="_blank">WhatsApp: +39 333 8689245</a><br>
                <a href="mailto:fbslatinempire@gmail.com">Email: fbslatinempire@gmail.com</a><br>
                <a href="https://www.tiktok.com/@fbslatinempire" target="_blank">TikTok: @fbslatinempire</a><br>
                <a href="https://www.facebook.com/fbslatinempire/" target="_blank">Facebook: FBS Latin Empire</a><br>
                <a href="https://www.instagram.com/fbslatinempire" target="_blank">Instagram: @fbslatinempire</a><br>
            </p>
        </div>
    </div>
</body>

</html>