<?php
require_once('header.php');
//Redirect user to login page if not logged in
if (!isset($_SESSION['UserID']) && basename(__FILE__) != 'login.php' && basename(__FILE__) != 'study.php') {
    header("Location: " . URL . "login.php");
} else {
    $user_id = $_SESSION['UserID'];
}
?>

<div class="card">
    <div class="card-header">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newStudyModal">New</button>
    </div>
    <div class="card-body">

        <!-- Study Table -->
        <table id="studyTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Study ID</th>
                    <th>Recipient</th>
                    <th>Send Date</th>
                    <th>Sent?</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $studies = getStudies($user_id);
                while ($row = $studies->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <?php echo $row['StudyID']; ?>
                        </td>
                        <td>
                            <?php echo $row['RecipientEmail']; ?>
                        </td>
                        <td>
                            <?php echo $row['ScheduleTime']; ?>
                        </td>
                        <td>
                            <?php  if($row['ScheduleSent']) {echo 'Yes';} else {echo 'No';} ?>
                        </td>
                        <td><a type="button" class="btn btn-warn"
                                href="./study.php?id=<?php echo $row['StudyID'] ?>">View</a></td>
                        <td><?php if (!$row['ScheduleSent']) { echo '<form action="actions.php?type=delete" method="post"><input type="hidden" name="study_id" value="'.$row['StudyID'].'"/><button type="submit" class="btn btn-dark">Cancel</button></form>'; } ?></td>
                    </tr>
                <?php } ?>
        </table>


    </div>
</div>
<!-- Study Table -->

<!-- New Study Modal -->
<div class="modal fade" id="newStudyModal" tabindex="-1" role="dialog" aria-labelledby="newStudyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newStudyModalLabel">New Study</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- New Study Form -->
            <form action="actions.php?type=create" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label for="email" class="col-sm-2 col-form-label">Recipient Email</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="email" name="email">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="email" class="col-sm-2 col-form-label">Send Date</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="date" name="date" placeholder="yyyy-MM-dd">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Upload Study</label>
                        <input class="form-control" type="file" id="file_upload" name="file_upload">
                    </div>
                    <input type="text" class="form-control" id="user_id" name="user_id" value="<?php echo $user_id ?>"
                        hidden>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Schedule Send</button>
                </div>
            </form>
            <!-- New Study Form -->

        </div>
    </div>
</div>
<!-- New Study Modal -->

<script>
    let table = new DataTable('#studyTable');

</script>

<?php require_once('footer.php'); ?>