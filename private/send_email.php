<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Load data from POST
$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'];
$email = $data['email'];
$qrDataURL = $data['qr']; // Base64 image string

// Convert base64 to image file
$qrPath = 'qr_' . time() . '.png';
file_put_contents($qrPath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $qrDataURL)));

$mail = new PHPMailer(true);

try {
    // SMTP setup (use your Gmail or domain SMTP)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'astrawintest@gmail.com'; // your email
    $mail->Password   = 'oetg yipa awjx ecvg';   // Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('astrawintest@gmail.com', 'ScannerEX');
    $mail->addAddress($email, $name);
    $mail->addAttachment($qrPath, 'QRCode.png');

    $mail->isHTML(true);
    $mail->Subject = 'Your ScannerEX QR Code';
    $mail->Body = "
  <div style='font-family: DM Sans, sans-serif; color: #333; line-height: 1.6; padding: 20px;'>
    <h2 style='color: #3b82f6;'>Hi $name,</h2>
    <p>Thank you for registering with <strong>ScannerEX</strong>.</p>
    <p>Your personalized QR code is attached to this email. Please save it securely, as it will be used for event check-ins or identification at the venue.</p>

    <div style='margin: 24px 0; padding: 16px; background-color: #f1f5f9; border-left: 4px solid #3b82f6;'>
      <p style='margin: 0; font-size: 15px;'>
        Need help or have questions? Just reply to this email — we're here to help.
      </p>
    </div>

    <p style='margin-top: 30px;'>Regards,<br><strong>ScannerEX Team</strong></p>

    <hr style='margin-top: 40px; border: none; border-top: 1px solid #e2e8f0;' />
    <p style='font-size: 13px; color: #6b7280;'>
      This is an automated message. Please do not reply directly to this email.
    </p>
  </div>
";


    $mail->send();
    unlink($qrPath); // delete after sending
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['error' => $mail->ErrorInfo]);
}
?>
