<?php
// Set the content type to JSON for the response
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Retrieve and sanitize form data
$company = isset($_POST['company']) ? strip_tags(trim($_POST['company'])) : '';
$name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';

// Basic validation
if (empty($company) || empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => '모든 필드를 올바르게 입력해주세요.']);
    exit;
}

// Email details
$to = 'jjjajh@naver.com'; // The email address you want to receive inquiries
$subject = '[Interview.AI 투자 문의] ' . $name . ' (' . $company . ')';

// Construct the email body
$body = "새로운 투자 문의가 도착했습니다.\n\n";
$body .= "----------------------------------------\n";
$body .= "회사/기관명: " . $company . "\n";
$body .= "성함: " . $name . "\n";
$body .= "이메일: " . $email . "\n";
$body .= "----------------------------------------\n\n";
$body .= "문의 내용:\n" . $message . "\n";

// Construct the email headers
// Using the sender's email in Reply-To is crucial for direct replies.
// Setting charset to UTF-8 to correctly handle Korean characters.
$headers = 'From: noreply@interview.ai' . "\r\n" . // A generic 'From' address from your domain
           'Reply-To: ' . $email . "\r\n" .
           'X-Mailer: PHP/' . phpversion() . "\r\n" .
           'Content-Type: text/plain; charset=UTF-8';

// Send the email
if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true, 'message' => '메일이 성공적으로 전송되었습니다.']);
} else {
    // This error message is for the user. More detailed logs should be kept on the server.
    echo json_encode(['success' => false, 'message' => '메일 전송에 실패했습니다. 서버 설정을 확인해주세요.']);
}

?>
