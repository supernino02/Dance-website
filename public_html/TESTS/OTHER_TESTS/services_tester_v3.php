<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Sandbox</title>
    <link rel="icon" href="MULTIMEDIA/icons/favicon.ico" type="image/x-icon">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        #container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding: 20px;
        }

        .actions {
            margin-bottom: 20px;
        }

        #serviceSelectContainer {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
            height: auto;
        }

        .param-container {
            display: none;
            margin-top: 10px;
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

        .response-container {
            border: 2px solid #ddd;
            height: auto;
            display: flex;
            flex-direction: column;
        }

        .context-menu {
            position: absolute;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            display: none;
            z-index: 1001;
        }

        .context-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .context-menu ul li {
            padding: 8px 12px;
            cursor: pointer;
        }

        .context-menu ul li:hover {
            background-color: #f2f2f2;
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

        function addServiceBlock(serviceName) {
            var params = serviceSignatures[serviceName];
            var block = $('<tr class="service-row"></tr>');
            var paramContainer = $('<td class="param-container"></td>');
            var blockTitle = $('<td class="block-title"></td>');
            var actions = $('<td class="action-icons"></td>');

            blockTitle.html(serviceName + '<button class="show-params-btn">Show Parameters</button>');
            actions.html('<button onclick="duplicateBlock(this)">Duplicate Block</button><button onclick="removeBlock(this)">Remove Block</button><button onclick="executeBlock(this)">Execute Block</button><button onclick="executeAllBlocks()">Execute All Blocks</button>');

            params.forEach(function(param) {
                var inputType = 'text';
                var defaultValue = param.has_default ? param.default : '';
                var inputClass = param.has_default ? 'optional' : 'needed';
                paramContainer.append('<input type="' + inputType + '" class="param-' + param.name + ' ' + inputClass + '" placeholder="' + param.name + '" value="' + defaultValue + '">');
            });

            block.append(blockTitle);
            block.append(paramContainer);
            block.append('<td class="response-container"><div class="additional-info-cell"></div><div class="value-cell"></div></td>');

            $("#serviceTable tbody").append(block);
            $("#serviceTable tbody").sortable({
                containment: "parent",
                placeholder: "ui-state-highlight"
            });
        }

        function removeBlock(button) {
            if (confirm("Are you sure you want to remove this block?")) {
                $(button).closest('tr').remove();
            }
        }

        function duplicateBlock(button) {
            var $block = $(button).closest('tr');
            var serviceName = $block.find('.block-title').text().split(' ')[0];
            addServiceBlock(serviceName);
        }

        function executeBlock(button) {
            var $block = $(button).closest('tr');
            var serviceName = $block.find('.block-title').text().split(' ')[0];
            var params = serviceSignatures[serviceName];
            var parameters = [];
            var valid = true;

            params.forEach(function(param) {
                var $input = $block.find('.param-' + param.name);
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
                $block.find('.response-container').css('border-color', borderColor);
                $block.find('.additional-info-cell').text(response.additional_info !== null ? response.additional_info : '');

                var valueCell = $block.find('.value-cell');
                if (response.value !== null) {
                    var valueText = JSON.stringify(response.value, null, 2);
                    valueCell.text(valueText);
                    if (valueText.length > 300) {
                        valueCell.addClass('overflowed');
                    } else {
                        valueCell.removeClass('overflowed');
                    }
                } else {
                    valueCell.text('');
                }
            });
        }

        function executeAllBlocks() {
            $("#serviceTable .service-row").each(function() {
                var $block = $(this);
                var serviceName = $block.find('.block-title').text().split(' ')[0];
                var params = serviceSignatures[serviceName];
                var parameters = [];
                var valid = true;

                params.forEach(function(param) {
                    var $input = $block.find('.param-' + param.name);
                    var value = $input.val();
                    if (!value && !param.has_default) {
                        $input.addClass('invalid');
                        valid = false;
                    } else {
                        $input.removeClass('invalid');
                    }
                    parameters.push(value);
                });

                if (valid) {
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
                        $block.find('.response-container').css('border-color', borderColor);
                        $block.find('.additional-info-cell').text(response.additional_info !== null ? response.additional_info : '');

                        var valueCell = $block.find('.value-cell');
                        if (response.value !== null) {
                            var valueText = JSON.stringify(response.value, null, 2);
                            valueCell.text(valueText);
                            if (valueText.length > 300) {
                                valueCell.addClass('overflowed');
                            } else {
                                valueCell.removeClass('overflowed');
                            }
                        } else {
                            valueCell.text('');
                        }
                    });
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
                        addServiceBlock(serviceName);
                    }
                });
                $("#clearBlocksBtn").click(function() {
                    $("#serviceTable tbody").empty();
                });
            });
        }

        $(document).ready(function() {
            loginAndFetchData();

            $("#serviceSelectBtn").click(function() {
                $("#serviceSelectContainer").toggle();
            });

            $(document).on('click', '.show-params-btn', function() {
                $(this).siblings('.param-container').toggle();
            });

            $(document).on('contextmenu', '.service-row', function(e) {
                e.preventDefault();
                var $contextMenu = $("#context-menu");
                $contextMenu.css({
                    top: e.pageY + 'px',
                    left: e.pageX + 'px',
                    display: 'block'
                });

                $contextMenu.data('block', $(this));
            });

            $(document).on('click', function() {
                $("#context-menu").hide();
            });

            $("#context-menu ul li").click(function() {
                var action = $(this).data('action');
                var $block = $("#context-menu").data('block');

                switch (action) {
                    case 'duplicate':
                        duplicateBlock($block.find('button')[0]);
                        break;
                    case 'remove':
                        removeBlock($block.find('button')[1]);
                        break;
                    case 'execute':
                        executeBlock($block.find('button')[2]);
                        break;
                    case 'executeAll':
                        executeAllBlocks();
                        break;
                }

                $("#context-menu").hide();
            });
        });
    </script>
</head>

<body>
    <div id="container">
        <div class="actions">
            <button id="serviceSelectBtn">Select Service</button>
        </div>

        <div id="serviceSelectContainer">
            <select id="serviceSelect">
                <option value="">--Select Service--</option>
            </select>
            <button id="createBlockBtn">Create Block</button>
            <button id="clearBlocksBtn">Clear All Blocks</button>
        </div>

        <h2>Service Blocks:</h2>
        <table id="serviceTable">
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Parameters</th>
                    <th>Response</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dynamically created blocks will go here -->
            </tbody>
        </table>

        <div id="context-menu" class="context-menu">
            <ul>
                <li data-action="duplicate">Duplicate Block</li>
                <li data-action="remove">Remove Block</li>
                <li data-action="execute">Execute Block</li>
                <li data-action="executeAll">Execute All Blocks</li>
            </ul>
        </div>

        <div id="overlay" class="overlay">
            <div id="overlay-content" class="overlay-content"></div>
        </div>
    </div>
</body>

</html>