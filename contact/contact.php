<?php
//Mailer
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Fpdf\Fpdf;
//Load Composer's autoloader
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
try{
    class PDF extends FPDF
    {
        // Cabecera de página
        function Header()
        {
            // Logo
            //$this->Image('../img/Bg-Full.jpg',0,0,210);
            // Arial bold 15
            $this->SetFont('Arial','B',20);
            // Movernos a la derecha
            $this->Cell(80);
            $this->SetTextColor(4,5,5);
            // Título
            $this->Cell(30,60,utf8_decode("Registro de inscripción al MCR"),3,0,'C');
            // Salto de línea
            $this->Ln(50);
        }

        // Pie de página
        function Footer()
        {
            // Posición: a 1,5 cm del final
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial','I',8);
            // Número de página
            //$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
        }
    }

    // Creación del objeto de la clase heredada
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',12);
    foreach($_POST as $idx => $value){
        $index = str_replace("_", " ", $idx);
        if(!empty($value) &&
            $idx != "accept-bases"){
            $pdf->MultiCell(0,10,utf8_decode("· " . $index . ": ". $value),0,1);
            $pdf->Cell(0,3,"",0,1);
        }
    }

  // attachment name
  $filename = strtolower(str_replace(" ", "_")) . ("Registro de inscripción al MCR_convocatoria2021_mcr.pdf"); 

    //$pdf->Output();
    $fpdfOutput = $pdf->Output("", "S");

    $email_to = explode(',', getenv("MAIL_TO") );
    //Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    $mail->IsSMTP();
    $mail->SMTPDebug = 0; // 2 = client and server messages
    $mail->Debugoutput = 'html';
    $mail->CharSet = 'UTF-8';
    $mail->Host = getenv('MAIL_HOST'); // SMTP server example
    $mail->SMTPAuth = true;                  // enable SMTP authentication
    $mail->Port = getenv('MIAL_PORT');                    // set the SMTP port for the GMAIL server
    $mail->Username = getenv('MAIL_USERNAME'); // SMTP account username example
    $mail->Password = getenv('MAIL_PASSWORD');        // SMTP account password example
    $mail->SMTPSecure = getenv('MAIL_SECURE');
    $mail->setFrom(getenv('MAIL_FROM'));
    $mail->addStringAttachment($fpdfOutput, $filename);

    foreach($email_to as $email)
    {
      $mail->addAddress( str_replace(" ", "", $email) );
    }

    $mail->AddCC($_POST['Email'], $_POST['Nombre y Apellido']);

    foreach ($_FILES as $input => $file){
      if(isset($file) && !empty($file["name"])){
      	//echo $file["tmp_name"];
        //$file = $_FILES["archivo1"];
        $mail->addStringAttachment(file_get_contents($file["tmp_name"]), $input . "_" .$file["name"]);
      }
    }
    $mail->Timeout = 20;
    $mail->Subject = utf8_decode($_POST["Especialidad"]) . " - Registro de inscripción al MCR - " . getenv('MAIL_SUBJECT');
    $mail->IsHTML(true);
    //Get Body
    $mail->Body = "Gracias por participar en la convocatoria del MCR 2021.
                   <br><br>
                   Por cualquier duda escribinos a stefanianicola@gmail.com";
    $mail->send();
    echo json_encode([ "success" => getenv('MSG_SUCCESS'), "success_server" => 'Message has been sent']);
} catch (Exception $e) {
    echo json_encode([ "error" => getenv('MSG_ERROR'), "error_server" => $mail->ErrorInfo]);
}
?>