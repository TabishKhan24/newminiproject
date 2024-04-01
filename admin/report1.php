<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
// Assuming you have already established a database connection

// Fetch all student IDs who have given feedback
$feedback_query = $conn->query("SELECT DISTINCT student_id FROM evaluation_list");
$students_with_feedback = array();
while ($row = $feedback_query->fetch_assoc()) {
    $students_with_feedback[] = $row['student_id'];
}

// Fetch all student IDs from your school's students table
$all_students_query = $conn->query("SELECT id FROM student_list");
$all_students = array();
while ($row = $all_students_query->fetch_assoc()) {
    $all_students[] = $row['id'];
}

// Find the student IDs who have not given feedback
$students_without_feedback = array_diff($all_students, $students_with_feedback);
$student_names = array();
foreach ($students_without_feedback as $student_id) {
    $student_query = $conn->query("SELECT firstname, lastname FROM student_list WHERE id = $student_id");
    $student_data = $student_query->fetch_assoc();
    $student_names[] = $student_data['firstname'] . ' ' . $student_data['lastname'];
}


// Now you have an array $students_without_feedback containing the IDs of students who have not given feedback
// You can use this array to fetch other details about these students if needed

// Continue with your existing code...

$faculty_id = isset($_GET['fid']) ? $_GET['fid'] : '';
// Your existing PHP code continues from here...

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
$total_percentage = array(1 => 0, 2 => 0, 3 => 0);
foreach ($q_arr as $question) {
    for ($c = 1; $c <= 3; $c++) {
        $total_percentage[$c] += $question['rate_' . $c] ?? 0;
    }
}
?>
<!-- HTML code continues... -->



<div class="col-lg-12">
    <div class="callout callout-info">
        <div class="d-flex w-100 justify-content-center align-items-center">
            <label for="faculty">Select Faculty</label>
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
                    <h3 class="text-center">Evaluation Report</h3>
                    <hr>
                    <table width="100%">
                        <tr>
                            <td width="50%">
                                <p><b>Faculty: <span id="fname"></span></b></p>
                            </td>
                            <td width="50%">
                                <p><b>Academic Year: <span id="ay"><?php echo $_SESSION['academic']['year'] . ' ' . (ordinal_suffix($_SESSION['academic']['semester'])) ?> Semester</span></b></p>
                            </td>
                        </tr>
                        <tr>
                            <td width="50%">
                                <p><b>Class: <span id="classField"></span></b></p>
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
                    <p>3 = High, 2 = Moderate, 1 = Low</p>
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
            </div>
            </td>
        <?php endfor; ?>
        </tr>
    <?php endwhile; ?>
    <tr>
        <td>Total Percentage</td>
        <?php for ($c = 1; $c <= 3; $c++) : ?>
            <td><?php echo $total_percentage[$c]; ?>%</td>
        <?php endfor; ?>
    </tr>
    </tbody>
    </table>
<?php endwhile; ?>
        </div>
        <div>
            <div>
                <h5>Students Who Haven't Given Feedback:</h5>
                <table>
                    <tr>
                        <th style="width: 100px;">Student ID</th>
                        <th>Student Name</th>
                    </tr>
                    <?php
                    // Assuming you have already populated the $students_without_feedback and $student_names arrays
                    $count = max(count($students_without_feedback), count($student_names));
                    for ($i = 0; $i < $count; $i++) {
                        $student_id = isset($students_without_feedback[$i]) ? $students_without_feedback[$i] : '';
                        $student_name = isset($student_names[$i]) ? $student_names[$i] : '';
                    ?>
                        <tr>
                            <td style="width: 100px;"><?php echo $student_id ?></td>
                            <td><?php echo $student_name; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

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
                if (resp) {
                    resp = JSON.parse(resp)
                    if (Object.keys(resp).length <= 0) {
                        $('#class-list').html('<a href="javascript:void(0)" class="list-group-item list-group-item-action disabled">No data to be display.</a>')
                    } else {
                        $('#class-list').html('')
                        Object.keys(resp).map(k => {
                            $('#class-list').append('<a href="javascript:void(0)" data-json=\'' + JSON.stringify(resp[k]) + '\' data-id="' + resp[k].id + '" class="list-group-item list-group-item-action show-result">' + resp[k].class + ' - ' + resp[k].subj + '</a>')
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
            $('.show-result.active').removeClass('active')
            $(this).addClass('active')
        })
    }

    function load_report($faculty_id, $subject_id, $class_id) {
        if ($('#preloader2').length <= 0)
            start_load()
        $.ajax({
            url: 'ajax.php?action=get_report',
            method: "POST",
            data: {
                faculty_id: $faculty_id,
                subject_id: $subject_id,
                class_id: $class_id
            },
            error: function(err) {
                console.log(err)
                alert_toast("An Error Occured.", "error");
                end_load()
            },
            success: function(resp) {
                if (resp) {
                    resp = JSON.parse(resp)
                    if (Object.keys(resp).length <= 0) {
                        $('.rates').text('')
                        $('#tse').text('')
                        $('#print-btn').hide()
                    } else {
                        $('#print-btn').show()
                        $('#tse').text(resp.tse)
                        $('.rates').text('-')
                        var data = resp.data
                        Object.keys(data).map(q => {
                            Object.keys(data[q]).map(r => {
                                console.log($('.rate_' + r + '_' + q), data[q][r])
                                $('.rate_' + r + '_' + q).text(data[q][r].toFixed(2) + '%')
                            })
                        })
                    }

                }
            },
            complete: function() {
                end_load()
            }
        })
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
</script>