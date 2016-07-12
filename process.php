<?php
  require_once "vendor/autoload.php";

  // your secret key
  $secret = "6Lf1DwUTAAAAAFUz1ZUG-2QfWz1cbC0HxGqz0vaw";

  $recaptcha = new \ReCaptcha\ReCaptcha($secret);

  $send_to = 'icon@djicon.com';

  $errors = array();    // array to hold validation errors
  $data = array();      // array to pass back data

  // validate the variables ======================================================
  // if any of these variables don't exist, add an error to our $errors array

  if (empty($_POST['inputName']))
    $errors['name'] = 'Please add your name.';

  if (empty($_POST['inputEmail']))
    $errors['email'] = 'Please add your email.';

  if (empty($_POST['message']))
    $errors['message'] = 'Please include a message.';

  if (empty($_POST['g-recaptcha-response']))
    $errors['gcaptcha'] = 'Please prove you are not a robot.';

    

  // return a response ===========================================================

  // if there are any errors in our errors array, return a success boolean of false
  if ( ! empty($errors)) {

    // if there are items in our errors array, return those errors
    $data['success'] = false;
    $data['errors']  = $errors;
  } else {

    // if submitted check response
    // $response = $reCaptcha->verifyResponse(

    $resp = $recaptcha->verify(
      $_POST["g-recaptcha-response"],
      $_SERVER["REMOTE_ADDR"]
    );
    if ($resp->isSuccess()) {

      $name = $_POST['inputName'];
      $email = $_POST['inputEmail'];
      $message = 'Name: ' . $name . "\r\n" . 'Email: ' . $email . "\r\n" . 'Message: ' . $_POST['message']; 

      $mail = new PHPMailer;

      //$mail->SMTPDebug = 3;                               // Enable verbose debug output

      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = 'walterwhite96969@gmail.com';                 // SMTP username
      $mail->Password = 'brightwing';                           // SMTP password
      $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587;                                    // TCP port to connect to
      $mail->From = 'walterwhite96969@gmail.com';
      $mail->FromName = $name;
      $mail->addAddress('icon@djicon.com', 'Connie');     // Add a recipient
      $mail->addAddress('fractallian@gmail.com', 'Jeremy');
      $mail->addReplyTo($email, $name);

      // $mail->addAddress('ellen@example.com');               // Name is optional
      // $mail->addCC('cc@example.com');
      // $mail->addBCC('bcc@example.com');
      // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
      // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
      // $mail->isHTML(true);                                  // Set email format to HTML

      $mail->Subject = 'Connie & Jeremy Contact Form';
      $mail->Body    = $message;
      // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

      if(!$mail->send()) {
          $errors['emailSend'] = $mail->ErrorInfo;
          $data['mail'] = $mail;
          $data['success'] = false;
          $data['error'] = $errors;
      } else {
        // show a message of success and provide a true success variable
        $data['success'] = true;
        $data['message'] = 'Thank you!';
      }

    } else {
      $errors['badcaptcha'] = 'Sorry, incorrect captcha. ';
      $errors['captchErrorCodes'] = $resp->getErrorCodes();
      $data['success'] = false;
      $data['errors'] = $errors;
    }
  }

  // return all our data to an AJAX call
  echo json_encode($data);
 
 ?>
