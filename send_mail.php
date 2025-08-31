<?php
// 어떤 경우에도 PHP 오류가 HTML로 출력되지 않도록 합니다.
// 이것이 JSON 응답을 깨뜨리는 주된 원인입니다.
error_reporting(0);
ini_set('display_errors', 0);

// 응답 형식을 JSON으로 먼저 설정합니다.
header('Content-Type: application/json');

// JSON 응답을 위한 배열을 미리 준비합니다.
$response = ['success' => false, 'message' => ''];

// 요청 방식이 POST가 아닌 경우 차단합니다.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = '잘못된 요청 방식입니다.';
    echo json_encode($response);
    exit;
}

// 폼 데이터를 받고 간단한 유효성 검사를 합니다.
$company = isset($_POST['company']) ? strip_tags(trim($_POST['company'])) : '';
$name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';

if (empty($company) || empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = '모든 필드를 올바르게 입력해주세요.';
    echo json_encode($response);
    exit;
}

// 이메일 내용을 구성합니다.
$to = 'jjjajh@naver.com';
$subject = '[Interview.AI 투자 문의] ' . $name . ' (' . $company . ')';
$body = "새로운 투자 문의가 도착했습니다.\n\n"
      . "----------------------------------------\n"
      . "회사/기관명: " . $company . "\n"
      . "성함: " . $name . "\n"
      . "이메일: " . $email . "\n"
      . "----------------------------------------\n\n"
      . "문의 내용:\n" . $message . "\n";

// 헤더는 한글(UTF-8)과 답장 기능(Reply-To)을 위해 중요합니다.
$headers = 'From: noreply@interview.ai' . "\r\n" .
           'Reply-To: ' . $email . "\r\n" .
           'X-Mailer: PHP/' . phpversion() . "\r\n" .
           'Content-Type: text/plain; charset=UTF-8';

// mail() 함수 실행 시 오류가 발생해도 @ 심볼로 인해 PHP 오류가 출력되지 않습니다.
// 오직 함수의 반환값(true/false)으로만 성공 여부를 판단합니다.
if (@mail($to, $subject, $body, $headers)) {
    $response['success'] = true;
    $response['message'] = '메일이 성공적으로 전송되었습니다.';
} else {
    // mail() 함수가 실패했을 때의 응답입니다.
    // 대부분 서버의 메일 기능 설정 문제입니다.
    $response['message'] = '메일 전송에 실패했습니다. 서버 관리자에게 문의해주세요.';
}

// 최종적으로 JSON 응답을 출력합니다.
echo json_encode($response);
?>

