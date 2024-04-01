<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php $faculty_id = isset($_GET['fid']) ? $_GET['fid'] : ''; ?>
<?php
function ordinal_suffix($num)
{
    $num = $num % 100; // protect against large numbers
    if ($num < 11 || $num > 13) {
        switch ($num % 10) {
            case 1:
                return $num . 'st';
            case 2:
                return $num . 'nd';
            case 3:
                return $num . 'rd';
        }
    }
    return $num . 'th';
}
?>
<div class="col-lg-12">
    <div class="callout callout-info">
        <div class="d-flex w-100 justify-content-center align-items-center">
            <label for="faculty">Faculty</label>
            <div class=" mx-2 col-md-4">
                <select name="" id="faculty_id" class="form-control form-control-sm select2">
                    <option value=""></option>
                    <?php
                    $faculty = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM faculty_list order by concat(firstname,' ',lastname) asc");
                    $f_arr = array();
                    $fname = array();
                    while ($row = $faculty->fetch_assoc()) :
                        $f_arr[$row['id']] = $row;
                        $fname[$row['id']] = ucwords($row['name']);
                    ?>
                        <option value="<?php echo $row['id'] ?>" <?php echo isset($faculty_id) && $faculty_id == $row['id'] ? "selected" : "" ?>><?php echo ucwords($row['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-1">
            <div class="d-flex justify-content-end w-100">
                <button class="btn btn-sm btn-success bg-gradient-success" style="display:none" id="print-btn"><i class="fa fa-print"></i> Print</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="callout callout-info">
                <div class="list-group" id="class-list">

                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="callout callout-info" id="printable">
                <div>
                    <h3 class style="text-align:left"><img src="assets/uploads/Diems.png" alt="Logo img" align="left"></h3>
                    <h3 class="text-center">Evaluation Report</h3>
                    <hr>
                    <table width="100%">
                        <tr>
                            <td width="50%">
                                <p><b>Faculty: <span id="fname"></span></b></p>
                            </td>
                            <td width="50%">
                                <p><b>Academic Year: <span id="ay"><?php echo $_SESSION['academic']['year']?></span></b> <b>Semester: <span id="ay"><?php echo (ordinal_suffix($_SESSION['academic']['semester']))?></span></b> <b>: <span id="ay"><?php echo ($_SESSION['academic']['feedback_type'])?></span></b></p>
                            </td>
                        </tr>
                        <tr>
                            <td width="50%">
                                <p><b>Class: <span id="classField"></span></b><b> - <span id="batchField"></span></b></p>
                            </td>
                            <td width="50%">
                                <p><b>Subject: <span id="subjectField"></span></b></p>
                            </td>
                        </tr>
                    </table>
                    <p class=""><b>Total Student Evaluated: <span id="tse"></span></b></p>
                </div>
                <fieldset class="border border-info p-2 w-100">
                    <legend class="w-auto">Rating Legend</legend>
                    <p>3 = High, 2 = Moderate, 1 = Low </p>
                </fieldset>
                <?php
                $q_arr = array();
                $criteria = $conn->query("SELECT * FROM criteria_list where id in (SELECT criteria_id FROM question_list where academic_id = {$_SESSION['academic']['id']} ) order by abs(order_by) asc ");
                while ($crow = $criteria->fetch_assoc()) :
                ?>
                    <table class="table table-condensed wborder">
                        <thead>
                            <tr class="bg-gradient-secondary">
                                <th class=" p-1"><b><?php echo $crow['criteria'] ?></b></th>
                                <th width="5%" class="text-center">1</th>
                                <th width="5%" class="text-center">2</th>
                                <th width="5%" class="text-center">3</th>

                            </tr>
                        </thead>
                        <tbody class="tr-sortable">
                            <?php
                            $questions = $conn->query("SELECT * FROM question_list where criteria_id = {$crow['id']} and academic_id = {$_SESSION['academic']['id']} order by abs(order_by) asc ");
                            while ($row = $questions->fetch_assoc()) :
                                $q_arr[$row['id']] = $row;
                            ?>
                                <tr class="bg-white">
                                    <td class="p-1" width="40%">
                                        <?php echo $row['question'] ?>
                                    </td>
                                    <?php for ($c = 1; $c <= 3; $c++) : ?>
                                        <td class="text-center">
                                            <span class="rate_<?php echo  $c . '_' . $row['id'] ?> rates"></span>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>
<style>
    .list-group-item:hover {
        color: black !important;
        font-weight: 700 !important;
    }
</style>
<noscript>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.wborder tr,
        table.wborder td,
        table.wborder th {
            border: 1px solid gray;
            padding: 3px
        }

        table.wborder thead tr {
            background: #6c757d linear-gradient(180deg, #828a91, #6c757d) repeat-x !important;
            color: #fff;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }
    </style>
</noscript>
<script>
    $(document).ready(function() {
        $('#faculty_id').change(function() {
            if ($(this).val() > 0)
                window.history.pushState({}, null, './index.php?page=report&fid=' + $(this).val());
            load_class()
        })
        if ($('#faculty_id').val() > 0)
            load_class()
    })

    function load_class() {
        start_load()
        var fname = <?php echo json_encode($fname) ?>;
        $('#fname').text(fname[$('#faculty_id').val()])
        $.ajax({
            url: "ajax.php?action=get_class",
            method: 'POST',
            data: {
                fid: $('#faculty_id').val()
            },
            error: function(err) {
                console.log(err)


                alert_toast("An error occured", 'error')
                end_load()
            },
            success: function(resp) {
                console.log(resp)
                if (resp) {
                    resp = JSON.parse(resp)
                    if (Object.keys(resp).length <= 0) {
                        $('#class-list').html('<a href="javascript:void(0)" class="list-group-item list-group-item-action disabled">No data to display.</a>')
                    } else {
                        $('#class-list').html('')
                        Object.keys(resp).map(k => {
                            $('#class-list').append('<a href="javascript:void(0)" data-json=\'' + JSON.stringify(resp[k]) + '\' data-id="' + resp[k].id + '" class="list-group-item list-group-item-action show-result">' + resp[k].class + ' - ' + resp[k].batch + ' - ' + resp[k].subj +'</a>')
                        })

                    }
                }
            },
            complete: function() {
                end_load()
                anchor_func()
                if ('<?php echo isset($_GET['rid']) ?>' == 1) {
                    $('.show-result[data-id="<?php echo isset($_GET['rid']) ? $_GET['rid'] : '' ?>"]').trigger('click')
                } else {
                    $('.show-result').first().trigger('click')
                }
            }
        })
    }

    function anchor_func() {
        $('.show-result').click(function() {
            var vars = [],
                hash;
            var data = $(this).attr('data-json')
            data = JSON.parse(data)
            var _href = location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for (var i = 0; i < _href.length; i++) {
                hash = _href[i].split('=');
                vars[hash[0]] = hash[1];
            }
            window.history.pushState({}, null, './index.php?page=report&fid=' + vars.fid + '&rid=' + data.id);
            load_report(vars.fid, data.sid, data.id);
            $('#subjectField').text(data.subj)
            $('#classField').text(data.class)
            $('#batchField').text(data.batch)
            $('.show-result.active').removeClass('active')
            $(this).addClass('active')
        })
    }

    function calculateAveragePercentage(data) {
        var averagePercentages = {};

        // Iterate over each rating category (1, 2, 3)
        for (var rating = 1; rating <= 3; rating++) {
            var totalPercentage = 0;
            var count = 0;

            // Iterate over each question and sum up the percentage for the current rating
            var totalPercentage = 0;
            var count = 0;
            var totalQuestions = Object.keys(data).length;

            Object.keys(data).forEach(function(question) {
                if (data[question][rating]) {
                    totalPercentage += parseFloat(data[question][rating]);
                    count++;
                }
            });

            // Calculate the average percentage for the current rating category
            var averagePercentage = totalQuestions > 0 ? (totalPercentage / totalQuestions) : 0;


            // Store the average percentage for the current rating category
            averagePercentages[rating] = averagePercentage;
        }

        return averagePercentages;
    }

    // Update load_report function to include the calculation and display of average percentages
    function load_report($faculty_id, $subject_id, $class_id, $batch_id, ) {
        if ($('#preloader2').length <= 0)
            start_load();
        $.ajax({
            url: 'ajax.php?action=get_report',
            method: "POST",
            data: {
                faculty_id: $faculty_id,
                subject_id: $subject_id,
                class_id: $class_id,
                batch_id: $batch_id,
            },
            error: function(err) {
                console.log(err);
                alert_toast("An Error Occured.", "error");
                end_load();
            },
            success: function(resp) {
                if (resp) {
                    resp = JSON.parse(resp);
                    if (Object.keys(resp).length <= 0) {
                        $('.rates').text('');
                        $('#tse').text('');
                        $('#print-btn').hide();
                    } else {
                        $('#print-btn').show();
                        $('#tse').text(resp.tse);
                        $('.rates').text('-');
                        var data = resp.data;
                        Object.keys(data).map(q => {
                            Object.keys(data[q]).map(r => {
                                console.log($('.rate_' + r + '_' + q), data[q][r]);
                                $('.rate_' + r + '_' + q).text(data[q][r].toFixed(2) + '%');
                            });
                        });
                        if (Object.keys(data).length > 0) {
                            // Generate pie charts
                            Object.keys(data).map(q => {
                                createPieChart(q, data[q]);
                            });  

                            // Calculate and display average percentages for each rating category
                            var averagePercentages = calculateAveragePercentage(data);

                            var averagePercentageString = Object.keys(averagePercentages).map(function(rating) {
                                return averagePercentages[rating].toFixed(2) + '%';
                            }).join(' '); // Join the average percentages with a space

                            var newRow = '<tr>';
                            newRow += '<td colspan="20" class="text-left"><b>Average Percentage: </b>' + '&nbsp;'.repeat(165);
                            for (var i = 0; i < averagePercentageString.length; i++) {
                                if (averagePercentageString[i] === '%' && i === averagePercentageString.length - 1) {
                                    newRow +='<b>' + averagePercentageString[i] + '</b>'; // Add '%' without any space if it's the last character
                                } else if (averagePercentageString[i] === '%') {
                                    newRow += '<b>' + averagePercentageString[i] + '</b>' + '&nbsp;'.repeat(18); // Add '%' with 17 spaces if it's not the last character
                                } else {
                                    newRow += '<b>' + averagePercentageString[i] + '</b>';
                                }
                            }
                            newRow += '</td>';
                            newRow += '</tr>';

                            $('.tr-sortable').append(newRow);

                            // Print the max statement only once
                            var maxRating = Object.keys(averagePercentages).reduce((a, b) => averagePercentages[a] > averagePercentages[b] ? a : b);
                            var maxStatement = '';
                            if (maxRating == 1) {
                                maxStatement = '<b>Your performance in this feedback is LOW (' + averagePercentages[maxRating].toFixed(2) + '%).</b>';
                            } else if (maxRating == 2) {
                                maxStatement = '<b>Your performance in this feedback is MODERATE (' + averagePercentages[maxRating].toFixed(2) + '%).</b>';
                            } else if (maxRating == 3) {
                                maxStatement = '<b>Your performance in this feedback is HIGH (' + averagePercentages[maxRating].toFixed(2) + '%).</b>';
                            }

                            if ($('.max-statement').length === 0) {
                                $('.tr-sortable').append('<tr class="max-statement"><td colspan="4" class="text-center">' + maxStatement + '</td></tr>');
                            }
                        }
                    }

                }
            },
            complete: function() {
                end_load();
            }
        });
    }
    function createPieChart(question, data) {
    // Create canvas dynamically below the table
    var canvas = document.createElement('canvas');
    canvas.id = `pieChart_${question}`;
    document.getElementById('printable').appendChild(canvas);

    var ctx = canvas.getContext('2d');

    // Customize these colors as needed
    var colors = ['#36A2EB', '#FF6384', '#FFCE56'];

    // Format data to display up to 2 decimal places
    if (Array.isArray(data)) {
        data = data.map(function(value) {
            return parseFloat(value.toFixed(2));
        });
    } else if (typeof data === 'object') {
        for (var key in data) {
            if (data.hasOwnProperty(key)) {
                data[key] = parseFloat(data[key].toFixed(2));
            }
        }
    }

    var pieChart;

    if (Array.isArray(data)) {
        pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Yes', 'No', 'Don\'t Know'],
                datasets: [{
                    data: data,
                    backgroundColor: colors
                }]
            },
            options: {
                title: {
                    display: true,
                    text: `Responses for Question: ${question}`
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 15,
                        fontSize: 12
                    }
                }
                // Add other customization options as needed
            }
        });
    } else if (typeof data === 'object') {
        pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: colors
                }]
            },
            options: {
                title: {
                    display: true,
                    text: `Responses for Question: ${question}`
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 15,
                        fontSize: 12
                    }
                }
                // Add other customization options as needed
            }
        });
    } else {
        console.log('Invalid data format for question:', question, data);
    }
}

    $('#print-btn').click(function() {
        start_load()
        var ns = $('noscript').clone()
        var content = $('#printable').html()
        ns.append(content)
        var nw = window.open("Report", "_blank", "width=900,height=700")
        nw.document.write(ns.html())
        nw.document.close()
        nw.print()
        setTimeout(function() {
            nw.close()
            end_load()
        }, 750)
    });
    /*$('#print-btn').click(function(){
        start_load();
        var facultyName = $('#fname').text().replace(/[^a-z0-9]/gi, '_');
        var subjectName = $('#subjectField').text().replace(/[^a-z0-9]/gi, '_');
        var fileName = facultyName + '_' + subjectName + '_Report.pdf';

        var content = $('#printable').get(0); // Get DOM element instead of HTML content

        // Convert HTML element to PDF
        html2pdf().from(content).save(fileName).then(function() {
            end_load();
        });
    });*/
</script>