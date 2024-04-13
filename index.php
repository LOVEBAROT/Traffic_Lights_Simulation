<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traffic Lights</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="CSS/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        .light {
            width: 80px;
            height: 80px;
            background-color: white;
            border: 1px solid #000;
            border-radius: 50%;
        }

        .dur-box-wrap {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="dur-box-wrap">
        <p>Demonstrate traffic lights system</p>
        <!-- Form -->
        <form id="lightsForm">
            <div class="form-group">
                <label for="redDuration">Red Duration (seconds):</label>
                <input type="number" id="redDuration" class="form-control durBox" min="1" max="200" required>
            </div>
            <div class="form-group">
                <label for="greenDuration">Green Duration (seconds):</label>
                <input type="number" id="greenDuration" class="form-control durBox" min="1" max="200" required>
            </div>
            <div class="form-group">
                <label for="yellowDuration">Yellow Duration (seconds):</label>
                <input type="number" id="yellowDuration" class="form-control durBox" min="1" max="200" required>
            </div>
            <div class="form-group">
                <label for="sequence">Sequence (comma-separated box numbers):</label>
                <input type="text" id="sequence" class="form-control" placeholder="e.g., 2,1,3,4" required>
            </div>
            <button type="submit" class="btn btn-primary">Start</button>
            <button type="button" onclick="stopLights()" class="btn btn-danger">Stop</button>
        </form>
    </div>
    <!-- Traffic Lights Table -->
    <table class="table" border="1">
        <thead>
        <tr>
            <th scope="col">Traffic light 1</th>
            <th scope="col">Traffic light 2</th>
            <th scope="col">Traffic light 3</th>
            <th scope="col">Traffic light 4</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <div class="light" data-color="red"></div>
                <div class="light" data-color="green"></div>
                <div class="light" data-color="yellow"></div>
            </td>
            <td>
                <div class="light" data-color="red"></div>
                <div class="light" data-color="green"></div>
                <div class="light" data-color="yellow"></div>
            </td>
            <td>
                <div class="light" data-color="red"></div>
                <div class="light" data-color="green"></div>
                <div class="light" data-color="yellow"></div>
            </td>
            <td>
                <div class="light" data-color="red"></div>
                <div class="light" data-color="green"></div>
                <div class="light" data-color="yellow"></div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<!-- jQuery -->
<script src="JS/jquery.min.js"></script>
<!-- Bootstrap JS -->
<script src="JS/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        // Form submission handler
        $('#lightsForm').submit(function (event) {
            event.preventDefault(); // Prevent form submission

            // Validation
            var isValid = true;
            $('.durBox').each(function () {
                if (!$(this).val() || isNaN($(this).val()) || $(this).val() < 1 || $(this).val() > 200) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            var sequence = $('#sequence').val().split(',');
            if (!$.isNumeric(sequence.join('')) || !sequence.every(function (num) {
                return num >= 1 && num <= 4;
            }) || sequence.length < 4) {
                isValid = false;
                $('#sequence').addClass('is-invalid');
            } else {
                $('#sequence').removeClass('is-invalid');
            }

            // Start lights if validation passed
            if (isValid) {
                startLights();
            } else {
                alert('Please fix the errors in the form.');
            }
        });
    });

    // Variables for controlling lights
    let count_down;
    let start_time;
    let remain_time;
    let light_dur_1;
    let active_light = [];
    let is_timer_paused = false;
    let sequence = [];

    // Function to start lights
    function startLights() {
        $('.lights-start').prop('disabled', true); // Disable start button
        is_timer_paused = false;
        if (active_light.length > 0) {
            updateLights(sequence, remain_time, active_light[0], active_light[1], active_light[2]);
        } else {
            sequence = $('#sequence').val().split(',');
            light_dur_1 = parseInt($('#redDuration').val()) * 1000;
            updateLights(sequence, light_dur_1);
        }
    }

    // Function to update lights
    function updateLights(sequence, light_dur, curr_box_index = 0, curr_light_col_elem = 0, light_index = 0) {
        curr_box_index = sequence[0];
        curr_light_col_elem = $('tbody tr td:nth-child(' + curr_box_index + ') div');
        switchLights(sequence, light_dur, curr_box_index, curr_light_col_elem, light_index)
    }

    // Function to switch lights
    function switchLights(sequence, light_dur, curr_box_index, curr_light_col_elem, light_index) {
        let current_light_box_color = curr_light_col_elem[light_index].getAttribute("data-color");
        curr_light_col_elem[light_index].style.backgroundColor = current_light_box_color;
        start_time = new Date().getTime();
        active_light = [curr_box_index, curr_light_col_elem, light_index];
        count_down = setTimeout(function () {
            curr_light_col_elem[light_index].style.backgroundColor = "#fff";
            light_index = light_index + 1;
            light_dur_1 = light_dur;
            if (light_index >= curr_light_col_elem.length) {
                sequence.shift();
                light_index = 0;
            }
            if (sequence.length > 0) {
                light_dur = parseInt(document.querySelectorAll(".dur-box-wrap .durBox")[light_index].value) * 1000;
                updateLights(sequence, light_dur, curr_box_index, curr_light_col_elem, light_index);
            } else {
                active_light = [];
                is_timer_paused = false;
            }
        }, light_dur);
    }

    // Function to stop lights
    function stopLights() {
        if (!is_timer_paused) {
            is_timer_paused = true;
            let currentTime = new Date().getTime();
            remain_time = ((light_dur_1 * 1000) - ((start_time - currentTime) / 1000)) / 1000;
            $('.lights-start').prop('disabled', false); // Enable start button
            clearTimeout(count_down);
        }
    }
</script>
</body>
</html>
