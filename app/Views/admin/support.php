<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Lịch sử hỗ trợ</h1>
      <p class="text-sm text-slate-500">Tối đa 200 tin nhắn gần nhất từ khách hàng.</p>
    </div>
    <a href="<?= base_url('/admin') ?>" class="text-sm font-bold text-emerald-700">Quay lại quản trị</a>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
    <?php if (empty($messages)): ?>
      <p class="p-8 text-center text-sm text-slate-500">Chưa có tin nhắn hỗ trợ.</p>
    <?php else: ?>
      <div class="divide-y divide-slate-100">
        <?php foreach ($messages as $message): ?>
          <article class="p-4">
            <div class="flex flex-wrap justify-between gap-2 text-xs text-slate-500 mb-2">
              <span class="font-bold text-slate-700"><?= e($message['user_name']) ?> (<?= e($message['user_email']) ?>) · <?= $message['role'] === 'user' ? 'Khách hàng' : 'Trợ lý AI' ?></span>
              <time><?= e($message['created_at']) ?></time>
            </div>
            <p class="text-sm whitespace-pre-wrap break-words"><?= e($message['message']) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
