<?php
$study_id = $_GET['id'];
require_once './header.php';

//Redirect user if not logged in
if (!isset($_SESSION['UserID']) && basename(__FILE__) != 'login.php') {
    header("Location: " . URL . "login.php");
} else {
    $user_id = $_SESSION['UserID'];
}
?>



<!-- Study Email Table -->
<table id="studyEmailTable" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>Subject</th>
            <th>From</th>
            <th>Received Date</th>
            <th>Body</th>
            <th>Attachment</th>
        </tr>
    </thead>
    <tbody>
        <?php


        $study_emails = getStudyEmails($user_id, 'SUBJECT STUDY' . $study_id);

        foreach ($study_emails as $email) { ?>
            <tr>
                <td>
                    <?php echo $email['subject'] ?>
                </td>
                <td>
                    <?php echo $email['sender'] ?>
                </td>
                <td>
                    <?php echo $email['date']; ?>
                </td>
                <td>
                    <?php echo $email['body']; ?>
                </td>

                <td>
                    <form method="post" action="download.php">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                        <input type="hidden" name="email_number" value="<?php echo $email['emailNumber']; ?>">
                        <input type="hidden" name="search_string" value="STUDY<?php echo $study_id; ?>">
                        <button type="submit" id="download" name="download">Download</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
</table>

<!-- Study Email Table -->

<?php require_once './footer.php' ?>
<script>
    let table = new DataTable('#studyEmailTable');
</script>