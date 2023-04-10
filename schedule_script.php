<?php

require_once './functions.php';

$stmt = $mysqli->prepare('SELECT * FROM Study WHERE ScheduleTime >= CURDATE() AND ScheduleSent = 0;');
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) { 


    sendStudyEmail($row['UserID'], $row['RecipientEmail'], 'STUDY' . $row['StudyID'] . ' - RESPONSE REQUIRED for Study', 'You have been chosen to complete a study. </br></br> Please complete the attached document and reply below this line with the attached file.  Please do not rename the file or the system will not recognise it.', $row['AttachmentPath']);

    // Delete the temporary file
    unlink($row['AttachmentPath']);

    //Update Study table to show Study is sent
    $stmt = $mysqli->prepare('UPDATE Study SET ScheduleSent = 1 WHERE StudyID = ?');
    $stmt->bind_param("s", $row['StudyID']);
    $stmt->execute();

    echo 'Sent Study '.$row['StudyID']. '<br/>';
}
?>