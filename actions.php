<?php
require_once('./functions.php');

//Get action type from URL
switch ($_GET['type']) {

    case 'login':
        
        session_start();

        //If username and password populated, get UserID from database and set session variable
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $user_id = getUserID($_POST['email'], hash('md5', $_POST['password']));

            if ($user_id != null) {
                $_SESSION['UserID'] = $user_id;
                header("Location: " . URL . "index.php");
            } else {
                //Return to login page with error if either field is empty  
                $_SESSION['UserID'] = null;
                header("Location: " . URL . "login.php?status=error");
            }
        }
        break;

    case 'logout':
        session_destroy();
        header("Location: " . URL . "/login.php");
        break;

    case 'create':
        //If required fields populated, create study in database
        if (isset($_FILES['file_upload']) && isset($_POST['email']) && isset($_POST['date'])) {
            $file_name = $_FILES['file_upload']['name'];
            $file_tmp = $_FILES['file_upload']['tmp_name'];
            $file_size = $_FILES['file_upload']['size'];
            $file_type = $_FILES['file_upload']['type'];
            $file_error = $_FILES['file_upload']['error'];

            // Move the uploaded file to the temporary location
            $temp_file_path = 'uploads/' . $file_name;
            move_uploaded_file($file_tmp, $temp_file_path);

            $study_id = createStudy($_POST['email'], date('Y-m-d H:i:s', $POST['date']), $temp_file_path, $_POST['user_id']);

            header("Location: " . URL . "index.php");
        }


        break;
    
        case 'delete':
            deleteStudy($_POST['study_id']);
            header("Location: " . URL . "index.php");
            break;

    case 'downloadAttachment':
        //Download the email attachment from a given email
        $user_id = $_GET['user_id'];
        $email_id = $_GET['email_id'];
        $study_id = $_GET['study_id'];

        var_dump(downloadAttachment($user_id, $email_id, 'STUDY' . $study_id . ' - '));
        break;
}

?>