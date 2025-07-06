<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../MULTIMEDIA/icons/favicon.ico" type="image/x-icon">
    <title>AJAX Request Example</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        table {
            border-collapse: collapse;
        }

        .OK {
            background-color: lime;
        }

        .FAIL {
            background-color: red;
        }

        .ERROR {
            background-color: orange;
        }

        .table-container {
            width: 100%;
            height: 400px;
            /* Adjust the height as needed */
            overflow: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        path = "../../GATEWAY.php";

        function isScalar(value) {
            return /^(string|number|boolean)$/.test(typeof value);
        }

        $(document).ready(function() {
            updateServiceInput();
            $("#submitBtn").click(function() {
                var service = $("#serviceInput").val();
                var parametersInput = $("#parametersInput").val();
                var parameters;

                // Controlla se il campo dei parametri è vuoto
                if (parametersInput.trim() === "") {
                    parameters = [];
                } else {
                    // Dividi il valore del campo dei parametri in un array usando lo " " come delimitatore
                    parameters = parametersInput.split(" ");
                }

                // Effettua la richiesta AJAX
                $.ajax({
                    url: path, // Inserisci l'URL del tuo servizio qui
                    type: "POST",
                    dataType: "json",
                    data: {
                        "service": service,
                        "parameters": parameters,
                        "verbose": true
                    },
                    success: function(response) {
                        console.log(response)
                        if (!isScalar(response.value)) {
                            var formattedResponse = "";
                            // Itera attraverso le chiavi dell'array associativo
                            for (var key in response.value) {
                                // Aggiungi la chiave e il valore alla stringa di output, separati da ": "
                                formattedResponse += key + ": " + JSON.stringify(response.value[key]) + "<br>";
                            }
                            response.value = formattedResponse;
                        }
                        // Gestisci la risposta ricevuta
                        // Create table row using jQuery
                        var $newRow = $('<tr>', { class: response.result })
                            .append($('<td>').html(response.request_name))
                            .append($('<td>').html(response.request_args))
                            .append($('<td>').html(response.result))
                            .append($('<td>').html(response.additional_info))
                            .append($('<td>').append($('<pre>').html(response.value)))
                            .append($('<td>').html(response.role));
                        $("#resultTable tbody").append($newRow);

                        // Scroll to the bottom of the table after appending new row
                        var table = $(".table-container");
                        table.scrollTop(table[0].scrollHeight);

                        // Esegui la seconda richiesta AJAX solo dopo che la prima è stata completata con successo
                        updateServiceInput();
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            var defaultValue = "";

            // Funzione per aggiornare la select
            function updateServiceInput() {
                // Memorizza il valore attuale prima di svuotare la select
                defaultValue = $("#serviceInput").val();

                $("#serviceInput").empty(); // Rimuovi le opzioni esistenti
                // Effettua la richiesta AJAX
                $.ajax({
                    url: path, // Inserisci l'URL del tuo servizio qui
                    type: "POST",
                    dataType: "json",
                    data: {
                        "service": "listServices",
                        "parameters": []
                    },
                    success: function(response) {
                        if (response.result != "OK") {
                            // Display an alert with the error message
                            alert(response.result + "\n" + response.additional_info + "\n" + response.value);
                        } else {
                            // Process the successful response
                            $.each(response.value, function(index, valore) {
                                $("#serviceInput").append($("<option></option>").val(valore).text(valore));
                            });
                            // Restore the default value if present
                            if (defaultValue !== "") {
                                $("#serviceInput").val(defaultValue);
                            }
                        }
                    }
                });
            }

        });
    </script>
</head>

<body>
    <h2>Inserisci i dati e clicca sul pulsante:</h2>
    <select id="serviceInput">
        <option default>listServices</option>
    </select>
    <input type="text" id="parametersInput" placeholder="Inserisci i parametri separati da spazio"><br>
    <button id="submitBtn">Invia richiesta</button>

    <h2>Risultati:</h2>

    <div class="table-container">
        <table id="resultTable" border="1">
            <thead>
                <tr>
                    <th>Request Name</th>
                    <th>Request Args</th>
                    <th>Result</th>
                    <th>Additional Info</th>
                    <th width="800px">Value</th>
                    <th>Role<br>(pre)</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <tbody>
        <!-- Qui verranno inserite le righe dei risultati -->
    </tbody>
    </table>
</body>

</html>