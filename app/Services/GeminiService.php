<?php
namespace App\Services;

use RuntimeException;

class GeminiService {
    public function reply(array $history, string $question): string {
        $apiKey = getenv('VIETCAR_GEMINI_API_KEY');
        if ($apiKey === false || trim($apiKey) === '') {
            throw new RuntimeException('VIETCAR_GEMINI_API_KEY is not configured.');
        }
        if (!function_exists('curl_init')) {
            throw new RuntimeException('PHP cURL extension is not available.');
        }

        $contents = [];
        foreach ($history as $message) {
            $contents[] = [
                'role' => $message['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $message['message']]],
            ];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $question]]];

        $model = getenv('VIETCAR_GEMINI_MODEL') ?: 'gemini-2.5-flash';
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/'
            . rawurlencode($model) . ':generateContent';
        $payload = json_encode([
            'systemInstruction' => [
                'parts' => [[
                    'text' => 'Bạn là trợ lý hỗ trợ khách hàng của VietCar, một dịch vụ thuê xe tự lái tại Việt Nam. '
                        . 'Trả lời bằng tiếng Việt, ngắn gọn, lịch sự và chỉ hướng dẫn về thuê xe, quy trình đặt xe, '
                        . 'thanh toán, hủy chuyến và cách liên hệ hỗ trợ. Không tự khẳng định thông tin cụ thể về '
                        . 'đơn đặt xe vì bạn không truy cập được dữ liệu đơn hàng. Nếu không chắc chắn, hãy nói rõ '
                        . 'và hướng dẫn khách liên hệ nhân viên VietCar. Không yêu cầu khách gửi mật khẩu, mã OTP '
                        . 'hoặc toàn bộ thông tin thẻ thanh toán.'
                ]],
            ],
            'contents' => $contents,
            'generationConfig' => ['temperature' => 0.4, 'maxOutputTokens' => 512],
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $curl = curl_init($url);
        if ($curl === false) {
            throw new RuntimeException('Could not initialize Gemini request.');
        }
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-goog-api-key: ' . trim($apiKey),
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($response === false) {
            throw new RuntimeException('Gemini request failed: ' . $curlError);
        }
        $data = json_decode($response, true);
        if ($status < 200 || $status >= 300) {
            $apiMessage = $data['error']['message'] ?? 'Unknown Gemini API error.';
            throw new RuntimeException("Gemini returned HTTP {$status}: {$apiMessage}");
        }
        if (!is_array($data)) {
            throw new RuntimeException('Gemini returned invalid JSON.');
        }

        $answer = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');
        if ($answer === '') {
            throw new RuntimeException('Gemini returned an empty answer.');
        }
        return $answer;
    }
}
