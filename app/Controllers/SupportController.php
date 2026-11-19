<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\SupportModel;
use App\Services\GeminiService;
use RuntimeException;

class SupportController extends Controller {
    private SupportModel $supportModel;
    private GeminiService $gemini;

    public function __construct() {
        $this->supportModel = new SupportModel();
        $this->gemini = new GeminiService();
    }

    public function index(): void {
        $user = $this->requireLogin();
        $this->view('support/index', [
            'title' => 'Hỗ trợ khách hàng - VietCar',
            'messages' => $this->supportModel->getHistory((int)$user['id']),
        ]);
    }

    public function send(): void {
        $this->requireLogin();
        $this->verifyCsrfToken();
        $question = trim($_POST['message'] ?? '');
        if ($question === '' || strlen($question) > 2000) {
            $_SESSION['flash_error'] = 'Tin nhắn cần có nội dung và không vượt quá 2.000 ký tự.';
            $this->redirect('/support');
        }

        $user = current_user();
        $history = $this->supportModel->getHistory((int)$user['id'], 12);
        try {
            $answer = $this->gemini->reply($history, $question);
        } catch (RuntimeException $e) {
            error_log('VietCar support assistant error: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Trợ lý AI hiện chưa thể trả lời. Vui lòng thử lại sau hoặc liên hệ nhân viên hỗ trợ.';
            $this->redirect('/support');
        }

        $this->supportModel->saveExchange((int)$user['id'], $question, $answer);
        $this->redirect('/support');
    }
}
