<div class="max-w-md mx-auto px-4 py-12">
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-7">
    <h1 class="text-2xl font-extrabold text-slate-900">Tạo tài khoản</h1>
    <p class="text-sm text-slate-500 mt-1 mb-6">Đăng ký tài khoản khách hàng VietCar.</p>
    <form method="POST" action="<?= base_url('/register') ?>" class="space-y-4">
      <?= csrf_field() ?>
      <div>
        <label class="block text-sm font-semibold mb-1">Họ tên</label>
        <input name="name" required maxlength="100" autocomplete="name" class="w-full h-11 px-3 rounded-xl border border-slate-300">
      </div>
      <div>
        <label class="block text-sm font-semibold mb-1">Email</label>
        <input type="email" name="email" required maxlength="190" autocomplete="email" class="w-full h-11 px-3 rounded-xl border border-slate-300">
      </div>
      <div>
        <label class="block text-sm font-semibold mb-1">Mật khẩu</label>
        <input type="password" name="password" required minlength="8" autocomplete="new-password" class="w-full h-11 px-3 rounded-xl border border-slate-300">
        <p class="text-xs text-slate-500 mt-1">Tối thiểu 8 ký tự.</p>
      </div>
      <button class="w-full h-11 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl">Đăng ký</button>
    </form>
    <p class="text-sm text-center text-slate-600 mt-5">Đã có tài khoản? <a class="text-emerald-700 font-bold" href="<?= base_url('/login') ?>">Đăng nhập</a></p>
  </div>
</div>
