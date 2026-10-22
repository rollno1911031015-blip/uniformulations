<?php
ob_start();
ini_set("log_errors", 1);
ini_set("error_log", "error.log");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);

if (!isset($_SESSION))
    session_start();

 
 
 

 


try {
    $mysqli = $_SERVER['SERVER_NAME'] == 'localhost' ? new mysqli("localhost", "root", "", "dm-pharma-global") : new mysqli("localhost", "DB_USER", "DB_PASSWORD", "DB_NAME");

    $mysqli->set_charset("utf8mb4");
    date_default_timezone_set('Asia/Calcutta');
    $now = new DateTime();
    $mins = $now->getOffset() / 60;
    $sgn = ($mins < 0 ? -1 : 1);
    $mins = abs($mins);
    $hrs = floor($mins / 60);
    $mins -= $hrs * 60;
    $offset = sprintf('%+d:%02d', $hrs * $sgn, $mins);
    $stmt = $mysqli->prepare("SET time_zone='$offset';");
    $stmt->execute();
    $stmt->close();
} catch (Exception $e) {
    error_log($e->getMessage());
    echo 'Error connecting to database'; //Should be a message a typical user could understand
}





function redirect($url)
{
    header('Location:' . $url);
    exit();
}

function validate_phone($mobile)
{
    return preg_match('/^[0-9]{10}+$/', $mobile);
}

function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}



function generate_token($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    $max = strlen($codeAlphabet);

    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[random_int(0, $max - 1)];
    }
    return $token;
}

function alert($msg, $type, $title = "")
{
    $_SESSION['toastr']['msg_text'] = $msg;
    $_SESSION['toastr']['msg_type'] = $type;
    $_SESSION['toastr']['msg_title'] = $title;
}

function toaster()
{

    if (!empty($_SESSION['toastr']['msg_text'])) {
        switch ($_SESSION['toastr']['msg_type']) {
            case 'error':
                echo '<script>toastr.error("' . $_SESSION['toastr']['msg_text'] . '","' . $_SESSION['toastr']['msg_title'] . '");</script>';
                break;
            case 'success':
                echo '<script>toastr.success("' . $_SESSION['toastr']['msg_text'] . '","' . $_SESSION['toastr']['msg_title'] . '");</script>';
                break;
            case 'warning':
                echo '<script>toastr.warning("' . $_SESSION['toastr']['msg_text'] . '","' . $_SESSION['toastr']['msg_title'] . '");</script>';
                break;
            default:
                echo '<script>toastr.info("' . $_SESSION['toastr']['msg_text'] . '","' . $_SESSION['toastr']['msg_title'] . '");</script>';
                break;
        }
    }

    $_SESSION['toastr']['msg_text'] = "";
    $_SESSION['toastr']['msg_title'] = "";
    $_SESSION['toastr']['msg_type'] = "";
}

/*  function toaster(){       
       
            if(!empty($_SESSION['toastr']['msg_text'])){
                switch ($_SESSION['toastr']['msg_type']) {
                    case 'error':
                        echo '<script>swal("'.$_SESSION['toastr']['msg_title'].'","'.$_SESSION['toastr']['msg_text'].'","error");</script>';
                        break;
                    case 'success':
                        echo '<script>swal("'.$_SESSION['toastr']['msg_title'].'","'.$_SESSION['toastr']['msg_text'].'","success");</script>';
                        break;
                    case 'warning':
                        echo '<script>swal("'.$_SESSION['toastr']['msg_title'].'","'.$_SESSION['toastr']['msg_text'].'","warning");</script>';
                        break;                              
                    default:
                        echo '<script>swal("'.$_SESSION['toastr']['msg_title'].'","'.$_SESSION['toastr']['msg_text'].'","info");</script>';
                        break;
                }
            }         

            $_SESSION['toastr']['msg_text']="";
            $_SESSION['toastr']['msg_title']="";
            $_SESSION['toastr']['msg_type']="";          
        
    }*/





function current_url()
{
    $url = $_SERVER['REQUEST_URI'];
    if (isset($url)) $url1 = explode("?", $url);
    $validURL = $url1[0];
    return $validURL;
}

function email_log($to, $cc, $bcc, $subject, $body, $files, $status)
{
    $stmt = $GLOBALS['mysqli']->prepare("INSERT INTO `email_log`( `EmailTo`, `EmailCC`, `EmailBCC`, `Subject`, `Body`, `Files`, `Status`) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssss", $to, $cc, $bcc, $subject, $body, $files, $status);
    $stmt->execute();
}

function sms_log($to, $msg, $status)
{
    $stmt = $GLOBALS['mysqli']->prepare("INSERT INTO `sms_log`( `Recipients`, `Message`, `Status`) VALUES (?,?,?)");
    $stmt->bind_param("sss", $to, $msg, $status);
    $stmt->execute();
}


function validate_uplaoded_img($name)
{

    $valid_file_extensions = array(".jpg", ".jpeg", ".gif", ".png");
    $file_extension = strrchr($_FILES[$name]['name'], ".");
    if (in_array($file_extension, $valid_file_extensions)) {
        if (@getimagesize($_FILES[$name]["tmp_name"]) !== false) {
            return true;
        } else
            return false;
    } else
        return false;
}

function validate_otp($otp)
{
    if ($otp == $_SESSION['otp_code'])
        return true;
    else
        return false;
}




