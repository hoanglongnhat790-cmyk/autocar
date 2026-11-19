<div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">
  <div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Hỗ trợ VietCar</h1>
    <p class="text-sm text-slate-500 mt-1">Trợ lý AI có thể hướng dẫn về thuê xe và quy trình đặt xe. Lịch sử chat được lưu trong tài khoản của bạn.</p>
  </div>
  <section class="bg-white border border-slate-200 rounded-2xl p-5 space-y-4 mb-5" aria-label="Lịch sử hỗ trợ">
    <?php if (empty($messages)): ?>
      <p class="text-sm text-slate-500">Xin chào <?= e(current_user()['name']) ?>! Bạn cần VietCar hỗ trợ vấn đề gì?</p>
    <?php else: ?>
      <?php foreach ($messages as $message): ?>
        <div class="<?= $message['role'] === 'user' ? 'text-right' : 'text-left' ?>">
          <div class="inline-block max-w-[90%] text-left rounded-2xl px-4 py-3 <?= $message['role'] === 'user' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-800' ?>">
            <p class="text-sm whitespace-pre-wrap break-words"><?= e($message['message']) ?></p>
            <time class="block text-[10px] opacity-70 mt-2"><?= e($message['created_at']) ?></time>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
  <form method="POST" action="<?= base_url('/support/send') ?>" class="bg-white border border-slate-200 rounded-2xl p-4">
    <?= csrf_field() ?>
    <label for="support-message" class="block text-sm font-bold mb-2">Tin nhắn của bạn</label>
    <textarea id="support-message" name="message" rows="3" maxlength="2000" required placeholder="Nhập câu hỏi..." class="w-full p-3 rounded-xl border border-slate-300 text-sm"></textarea>
    <div class="flex justify-between items-center mt-3">
      <span class="text-xs text-slate-500">Không gửi mật khẩu hoặc mã OTP.</span>
      <button class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold">Gửi câu hỏi</button>
    </div>
  </form>
</div>
