<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($title ?? 'VietCar PHP MVC') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen">
  <header class="bg-white/95 backdrop-blur sticky top-0 z-40 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <a href="<?= base_url('/') ?>" class="flex items-center gap-2.5">
        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xl shadow-sm">
          <i class="fa-solid fa-car-side"></i>
        </div>
        <span class="text-xl font-extrabold tracking-tight text-slate-900">Viet<span class="text-emerald-600">Car</span> <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600">PHP MVC</span></span>
      </a>

      <nav class="flex items-center gap-5 text-sm font-medium">
        <a href="<?= base_url('/') ?>" class="text-slate-700 hover:text-emerald-600 font-semibold transition">Thuê xe</a>
        <a href="<?= base_url('/bookings') ?>" class="text-slate-700 hover:text-emerald-600 font-semibold transition">Chuyến của tôi</a>
        <?php if (\App\Core\Auth::check()): ?>
          <a href="<?= base_url('/customers') ?>" class="text-slate-700 hover:text-emerald-600 font-semibold transition">Khách hàng</a>
          <?php if (\App\Core\Auth::hasRole('admin')): ?>
            <a href="<?= base_url('/accounts') ?>" class="text-slate-700 hover:text-emerald-600 font-semibold transition">Tài khoản</a>
          <?php endif; ?>
          <a href="<?= base_url('/admin') ?>" class="text-slate-700 hover:text-emerald-600 font-semibold transition">Quản trị</a>
          <form method="POST" action="<?= base_url('/logout') ?>">
            <button type="submit" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:border-rose-300 hover:text-rose-600 transition">
              <i class="fa-solid fa-right-from-bracket mr-1"></i><?= e(\App\Core\Auth::user()['username']) ?>
            </button>
          </form>
        <?php else: ?>
          <a href="<?= base_url('/login') ?>" class="px-3.5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-emerald-600 transition">
            <i class="fa-solid fa-right-to-bracket mr-1"></i> Đăng nhập
          </a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
      <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between">
        <span><i class="fa-solid fa-circle-check text-emerald-600 mr-2"></i><?= e($_SESSION['flash_success']) ?></span>
        <?php unset($_SESSION['flash_success']); ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
      <div class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-xl text-sm font-semibold">
        <i class="fa-solid fa-circle-exclamation text-rose-600 mr-2"></i><?= e($_SESSION['flash_error']) ?>
        <?php unset($_SESSION['flash_error']); ?>
      </div>
    </div>
  <?php endif; ?>
  <main class="flex-1">
