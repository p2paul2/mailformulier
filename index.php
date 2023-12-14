
<?php

$honeypot = $_POST["firstname"] ?? "";
$name = $_POST["name"] ?? "";
$email= $_POST["email"] ?? "";
$subjectline= $_POST["onderwerp"] ?? "";
$message= $_POST["message"] ?? "";

if (!empty($honeypot)) return; 

$nameErr = $emailErr = $subjectlineErr = $messageErr = "";
$name = $email = $subjectline = $message = "";

if (isset($_SERVER["REQUEST_METHOD"]) &&  
$_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["name"])) {
    $nameErr = "Name is required";
  } else {
    $name = test_input($_POST["name"]);
    // check if name only contains letters and whitespace
    if (!preg_match("/^[0-9a-zA-Z-_' ]*$/",$name)) {
      $nameErr = "Spelersnaam mag geen speciale tekens bevatten!<br><br>";
    }
  }

  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
  } else {
    $email = test_input($_POST["email"]);
    // check if e-mail address is valid and well-formed
    if (!preg_match("/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,9})+$/",$email)) {
      $emailErr = "Ongeldig e-mailadres!<br><br>";
    }
  }

  if (empty($_POST["onderwerp"])) {
    $subjectline = "";
  } else {
    $subjectline = test_input($_POST["onderwerp"]);
    // check if subjectline only contains letters and whitespace
    if (!preg_match("/^[0-9a-zA-Z-' ]*$/",$subjectline)) {
      $subjectlineErr = "Onderwerp mag geen speciale tekens bevatten!<br><br>";
    }
  }

  if (empty($_POST["message"])) {
    $message = "";
  } else {
    $message = test_input($_POST["message"]);
  }

}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

?>

<span class="mailformerror"><?php echo $nameErr;?></span>
<span class="mailformerror"><?php echo $emailErr;?></span>
<span class="mailformerror"><?php echo $subjectlineErr;?></span>

<?php

$website = "TorchCraft.nl";
$to = "hulp@torchcraft.nl";
$subject = "$subjectline";
$txt = "Dit bericht is verstuurd vanaf " . $website . ":\r\n 
Spelersnaam: ". $name . "\r\n
E-mailadres: " . $email . "\r\n
Onderwerp: " . $subjectline . "\r\n
Bericht:\n" . $message;

    $tmp_name = $_FILES["bijlage"]["tmp_name"] ?? ""; // get the temporary file name of the file on the server
    $filename = $_FILES["bijlage"]["name"] ?? ""; // get the name of the file
    $size     = $_FILES["bijlage"]["size"] ?? ""; // get size of the file for size validation
    $type     = $_FILES["bijlage"]["type"] ?? ""; // get type of the file

if (!empty($tmp_name)) {
    $handle = fopen($tmp_name, "r"); // set the file handle only for reading the file
    $content = fread($handle, $size); // reading the file
    fclose($handle); // close upon completion
 
    $encoded_content = chunk_split(base64_encode($content));
}

    $boundary = md5("random"); // define boundary with a md5 hashed value

    $headers = "MIME-Version: 1.0\r\n"; // Defining the MIME version
    $headers .= "From: hulp@torchcraft.nl" . "\r\n" .
    "Reply-To:  $email \r\n";
    $headers .= "Content-Type: multipart/mixed;"; // Defining Content-Type
    $headers .= "boundary = $boundary\r\n"; //Defining the Boundary
         
    $body = "$txt\r\n\r\n";
    $body .= "--$boundary\r\n";
    $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $body .= chunk_split(base64_encode($txt));
         
    $body .= "--$boundary\r\n";
    $body .="Content-Type: $type; name=".$filename."\r\n";
    $body .="Content-Disposition: attachment; filename=".$filename."\r\n";
    $body .="Content-Transfer-Encoding: base64\r\n";
    $body .="X-Attachment-Id: ".rand(1000, 99999)."\r\n\r\n";
    $body .= $encoded_content; // Attaching the encoded file with email
  
if (empty($nameErr)&&empty($emailErr)&&empty($subjectlineErr)&&!empty($name)&&!empty($email)&&!empty($subjectline)&&!empty($message))
  {
mail($to, $subject, $body, $headers);
   
echo "<font color=\"#33cc00\"><b><i class=\"fa-solid fa-check\"></i> Je bericht is succesvol verstuurd!</b></font><br><br>";
  }   

?>

<div class="mailformulier">
  <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

    <input type="text" name="firstname" id="firstname" class="hidemobile hidedesktop">

    <input type="text" name="name" class="mailformregel" placeholder="Je spelersnaam" value="<?php echo $name;?>" required><br>
    <br>
    <input type="email" name="email" class="mailformregel" placeholder="Je e-mailadres" value="<?php echo $email;?>" required><br>
    <br>   
    <input type="text" name="onderwerp" class="mailformregel" placeholder="Het onderwerp" value="<?php echo $subjectline;?>" required><br>
    <br>
    <textarea name="message" class="mailformvak" placeholder="Je bericht" rows="7" required><?php echo $message;?></textarea><br>
    <br>
    <input type="file" name="bijlage"><br>
    <br>
    <input type="submit" class="mailformbutton" value="Versturen">

  </form>
</div>
