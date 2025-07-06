<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="MULTIMEDIA/icons/favicon.ico" type="image/x-icon">
    <title>AJAX Request Example</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .service-row {
            background-color: #f9f9f9;
            height: 50px;
        }

        .param-container input {
            margin-right: 5px;
        }

        .param-container input.invalid {
            border: 2px solid red;
        }

        .param-container input.needed {
            border: 2px solid black;
        }

        .param-container input.optional {
            border: 2px solid gray;
        }

        .response {
            margin-top: 5px;
        }

        .action-icons {
            cursor: pointer;
        }

        .action-icons img {
            width: 20px;
            height: 20px;
        }

        .value-cell,
        .additional-info-cell {
            max-height: 90px;
            overflow-y: hidden;
            position: relative;
        }

        .response-container {
            border: 2px solid #ddd;
            height: 100px;
            display: flex;
            flex-direction: column;
        }

        .value-cell,
        .additional-info-cell {
            border: none;
            white-space: pre-wrap;
            flex: 1;
        }

        .value-cell.overflowed::after {
            content: "...";
            position: absolute;
            bottom: 0;
            right: 0;
            cursor: pointer;
            background-color: white;
            padding: 0 5px;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            display: none;
        }

        .overlay-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            max-height: 80%;
            overflow-y: auto;
            max-width: 80%;
            white-space: pre-wrap;
        }
    </style>
    <script>
        var path = "../GATEWAY.php";
        var serviceSignatures = {};

        function isScalar(value) {
            return /^(string|number|boolean)$/.test(typeof value);
        }

        function fetchServiceSignatures(services) {
            var promises = services.map(function(serviceName) {
                return $.ajax({
                    url: path,
                    type: "POST",
                    dataType: "json",
                    data: {
                        "service": "getServiceSignature",
                        "parameters": [serviceName]
                    }
                }).done(function(response) {
                    if (response.result === "OK") serviceSignatures[serviceName] = response.value;
                });
            });
            $.when.apply($, promises).done(function() {
                populateServiceSelect();
                createServiceBlocks();
            });
        }

        function populateServiceSelect() {
            var $serviceSelect = $("#serviceSelect");
            $serviceSelect.empty();
            $serviceSelect.append('<option value="">--Select Service--</option>');
            $.each(Object.keys(serviceSignatures), function(index, serviceName) {
                $serviceSelect.append('<option value="' + serviceName + '">' + serviceName + '</option>');
            });
        }

        function addServiceRow(serviceName) {
            var params = serviceSignatures[serviceName];
            var row = $('<tr class="service-row"></tr>');
            row.append('<td class="action-icons"><img src="https://cdn4.iconfinder.com/data/icons/phone-interface-1/100/01_copy_color-512.png" onclick="duplicateRow(this)"><img src="https://cdn3.iconfinder.com/data/icons/streamline-icon-set-free-pack/48/Streamline-70-512.png" onclick="removeRow(this)"><img src="https://pic.onlinewebfonts.com/thumbnails/icons_488154.svg" onclick="executeService(this)"></td>');
            row.append('<td>' + serviceName + '</td>');

            var paramContainer = $('<td class="param-container"></td>');
            params.forEach(function(param) {
                var inputType = param.type === 'string' ? 'text' : 'number';
                var defaultValue = param.has_default ? param.default : '';
                var inputClass = param.has_default ? 'optional' : 'needed';
                paramContainer.append('<input type="' + inputType + '" class="param-' + param.name + ' ' + inputClass + '" ' +
                    'placeholder="' + param.name + '" value="' + defaultValue + '">');
            });

            row.append(paramContainer);
            row.append('<td class="response-container"><div class="additional-info-cell"></div><div class="value-cell"></div></td>');

            $("#serviceTable tbody").append(row);
            $("#serviceTable tbody").sortable({
                containment: "parent",
                placeholder: "ui-state-highlight"
            });
        }

        function removeRow(button) {
            if (confirm("Are you sure you want to remove this row?")) {
                $(button).closest('tr').remove();
            }
        }

        function duplicateRow(button) {
            var $row = $(button).closest('tr');
            var serviceName = $row.find('td:eq(1)').text();
            addServiceRow(serviceName);
        }

        function executeService(button) {
            var $row = $(button).closest('tr');
            var serviceName = $row.find('td:eq(1)').text();
            var params = serviceSignatures[serviceName];
            var parameters = [];
            var valid = true;

            params.forEach(function(param) {
                var $input = $row.find('.param-' + param.name);
                var value = $input.val();
                if (!value && !param.has_default) {
                    $input.addClass('invalid');
                    valid = false;
                } else {
                    $input.removeClass('invalid');
                }
                parameters.push(value);
            });

            if (!valid) return;

            $.ajax({
                url: path,
                type: "POST",
                dataType: "json",
                data: {
                    "service": serviceName,
                    "parameters": parameters,
                    "verbose": true
                }
            }).done(function(response) {
                var borderColor = response.result === "OK" ? "lime" : "red";
                $row.find('.response-container').css('border-color', borderColor);
                $row.find('.additional-info-cell').text(response.additional_info !== null ? response.additional_info : '');

                var valueCell = $row.find('.value-cell');
                if (response.value !== null) {
                    var valueText = JSON.stringify(response.value, null, 2);
                    valueCell.text(valueText);
                    if (valueText.length > 300) { // Arbitrary length to check overflow
                        valueCell.addClass('overflowed');
                    } else {
                        valueCell.removeClass('overflowed');
                    }
                } else {
                    valueCell.text('');
                }
            });
        }

        function updateServiceInput() {
            $.ajax({
                url: path,
                type: "POST",
                dataType: "json",
                data: {
                    "service": "listServices",
                    "parameters": []
                }
            }).done(function(response) {
                if (response.result === "OK") {
                    fetchServiceSignatures(response.value);
                }
            });
        }

        function loginAndFetchData() {
            $.ajax({
                url: path,
                type: "POST",
                dataType: "json",
                data: {
                    "service": "loginCredentials",
                    "parameters": ["luca@gmail.com", "luca"]
                }
            }).done(function() {
                updateServiceInput();
                $("#createBlockBtn").click(function() {
                    var serviceName = $("#serviceSelect").val();
                    if (serviceName) {
                        addServiceRow(serviceName);
                    }
                });
                $("#clearBlocksBtn").click(function() {
                    $("#serviceTable tbody").empty();
                });
            });
        }

        $(document).ready(function() {
            loginAndFetchData();

            // Overlay functionality for displaying large content
            $(document).on('click', '.value-cell.overflowed::after', function() {
                var valueContent = $(this).closest('.value-cell').text();
                $("#overlay-content").text(valueContent);
                $("#overlay").show();
            });

            $("#overlay").click(function() {
                $(this).hide();
            });
        });
    </script>
</head>

<body>
    <h2>Select a Service to Create Block:</h2>
    <select id="serviceSelect">
        <option value="">--Select Service--</option>
    </select>
    <button id="createBlockBtn">Create Row</button>
    <button id="clearBlocksBtn">Clear All Rows</button>

    <h2>Service Table:</h2>
    <table id="serviceTable">
        <thead>
            <tr>
                <th>Actions</th>
                <th>Service Name</th>
                <th>Parameters</th>
                <th>Response</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dynamically created rows will go here -->
        </tbody>
    </table>

    <div id="overlay" class="overlay">
        <div id="overlay-content" class="overlay-content"></div>
    </div>
</body>

</html>