<div class="max-w-md mx-auto px-4 py-16">
  <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
    <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl mb-5">
      <i class="fa-solid fa-right-to-bracket"></i>
    </div>
    <h1 class="text-2xl font-extrabold text-slate-900 mb-1">Đăng nhập hệ thống</h1>
    <p class="text-sm text-slate-500 mb-6">Quản lý khách hàng và tài khoản VietCar.</p>

    <form method="POST" action="<?= base_url('/login') ?>" class="space-y-4">
      <div>
        <label class="block text-xs font-bold text-slate-600 mb-1">Tên đăng nhập</label>
        <input type="text" name="username" required autofocus value="<?= e($username ?? '') ?>"
               class="w-full h-11 px-3 rounded-xl border border-slate-200 focus:border-emerald-500 outline-none" />
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-600 mb-1">Mật khẩu</label>
        <input type="password" name="password" required
               class="w-full h-11 px-3 rounded-xl border border-slate-200 focus:border-emerald-500 outline-none" />
      </div>
      <button type="submit" class="w-full h-11 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl">
        Đăng nhập
      </button>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-xs text-slate-500 leading-6">
      <p class="font-bold text-slate-700 mb-1">Tài khoản demo (sau khi import SQL):</p>
      <p><code>admin</code> / <code>Admin@123</code> — Quản trị viên</p>
      <p><code>staff</code> / <code>Staff@123</code> — Nhân viên</p>
      <p><code>an.nguyen</code> / <code>Customer@123</code> — Khách hàng</p>
    </div>
  </div>
</div>
