<?php

//PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';


//Config Variables
define('URL', 'http://localhost/');


//Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "KV6003";

$GLOBALS['mysqli'] = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

//Functions

//Return studies for a given user from the database
function getStudies($UserID)
{
    global $mysqli;
    $stmt = $mysqli->prepare('SELECT * FROM Study WHERE UserID = ? ORDER BY StudyID ASC');
    $stmt->bind_param("s", $UserID);
    $stmt->execute();
    return $stmt->get_result();
}

//Create a new entry in the Study table
function createStudy($email, $date, $attachment_path, $user_id)
{
    global $mysqli;
    $stmt = $mysqli->prepare('INSERT INTO Study (RecipientEmail, Active, ScheduleTime, AttachmentPath, UserID) VALUES (?, 1, ?, ?, ?)');
    $stmt->bind_param("ssss", $email, $date, $attachment_path, $user_id);
    $stmt->execute();

    return $mysqli->insert_id;
}

//Remove a Study from database by ID
function deleteStudy($study_id)
{
    global $mysqli;
    $stmt = $mysqli->prepare('DELETE FROM Study WHERE StudyID = ?');
    $stmt->bind_param("s", $study_id);
    $stmt->execute();

    return $mysqli->insert_id;
}

//Get emails for a Study using the subject
function getStudyEmails($user_id, $search_string)
{
    $user = getUserDetails($user_id);

    $inbox = imap_open('{' . $user['IMAPServer'] . ':993/imap/ssl/novalidate-cert}INBOX', $user['EmailUsername'], $user['EmailPassword']) or die('Cannot connect to IMAP: ' . imap_last_error());

    $emails = imap_search($inbox, $search_string);
    $result = array();
    if ($emails) {
        foreach ($emails as $email) {
            $overview = imap_fetch_overview($inbox, $email, 0);
            $body = imap_fetchbody($inbox, $email, 1);
            $result[] = array(
                'emailNumber' => $email,
                'sender' => $overview[0]->from,
                'subject' => $overview[0]->subject,
                'date' => $overview[0]->date,
                'body' => strip_tags($body)
            );
        }
    }
    imap_close($inbox);
    return $result;
}

//Download attachment for a given email
//TODO: Fix attachment downloads
function downloadAttachment($user_id, $email_number, $search_string)
{
    $user = getUserDetails($user_id);
    $inbox = imap_open('{' . $user['IMAPServer'] . ':993/imap/ssl/novalidate-cert}INBOX', $user['EmailUsername'], $user['EmailPassword']) or die('Cannot connect to IMAP: ' . imap_last_error());

    $attachment_data = '';
    $structure = imap_fetchstructure($inbox, $email_number);
    if (isset($structure->parts)) {
        foreach ($structure->parts as $part) {
            if (isset($part->disposition) && $part->disposition == 'attachment' && str_starts_with($part->dparameters[0]->value, $search_string)) {
                $attachment = imap_fetchbody($inbox, $email_number, $part->partnum);
                $encoding = $part->encoding;
                if ($encoding == 0) {
                    $attachment_data = $attachment;
                } elseif ($encoding == 1) {
                    $attachment_data = imap_8bit($attachment);
                } elseif ($encoding == 2) {
                    $attachment_data = imap_binary($attachment);
                } elseif ($encoding == 3) {
                    $attachment_data = imap_base64($attachment);
                } elseif ($encoding == 4) {
                    $attachment_data = quoted_printable_decode($attachment);
                }
                header("Content-Disposition: attachment; filename=\"" . $part->dparameters[0]->value . "\"");
                header("Content-Type: application/octet-stream");
                header("Content-Length: " . strlen($attachment_data));
                echo $attachment_data;
                exit;
            }
        }
    }
    imap_close($inbox);
}

//Send the email for a Study to a specified user
function sendStudyEmail($user_id, $recipient_address, $email_subject, $email_body, $file_path)
{
    $user = getUserDetails($user_id);

    $mail = new PHPMailer;

    $mail->isSMTP();
    $mail->Host = $user['SMTPServer'];
    $mail->SMTPAuth = true;
    $mail->Username = $user['EmailUsername'];
    $mail->Password = $user['EmailPassword'];
    $mail->SMTPSecure = 'tls';
    $mail->Port = $user['SMTPPort'];

    $mail->From = $user['EmailUsername'];
    $mail->FromName = 'KV6003';
    $mail->addAddress($recipient_address);
    $mail->addReplyTo($user['EmailUsername'], 'KV6003');
    $mail->isHTML(true);
    $mail->Subject = $email_subject;
    $mail->Body = $email_body;

    // Attach the file
    $mail->addAttachment($file_path);

    if (!$mail->send()) {
        echo 'Message could not be sent.';
        return 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        return 'Message has been sent.';
    }
}


function getStudyByID($StudyID)
{
    global $mysqli;
    $stmt = $mysqli->prepare('SELECT * FROM Study WHERE StudyID = ?');
    $stmt->bind_param("s", $StudyID);
    $stmt->execute();
    return $stmt->get_result();
}

//Get a UserID by Email and Password
function getUserID($UserEmail, $UserPassword)
{
    global $mysqli;
    $stmt = $mysqli->prepare('SELECT UserID FROM User WHERE UserEmail = ? AND UserPassword = ? LIMIT 1');
    $stmt->bind_param("ss", $UserEmail, $UserPassword);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()["UserID"];
}

//Get User details by UserID
function getUserDetails($user_id)
{
    global $mysqli;
    $stmt = $mysqli->prepare('SELECT * FROM User WHERE UserID = ? LIMIT 1');
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

?>