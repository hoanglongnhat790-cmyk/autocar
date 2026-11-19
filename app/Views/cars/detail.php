<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-emerald-600 mb-6">
    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách xe
  </a>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Car Info -->
    <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200">
      <img src="<?= e($car['image']) ?>" alt="<?= e($car['name']) ?>" class="w-full h-72 object-cover rounded-xl mb-5" />
      <div class="flex items-center justify-between mb-2">
        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold"><?= e($car['brand']) ?> • <?= e($car['category']) ?></span>
        <span class="text-amber-500 font-bold text-sm"><i class="fa-solid fa-star"></i> <?= e($car['rating']) ?> (<?= (int)$car['trips'] ?> chuyến)</span>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-900 mb-2"><?= e($car['name']) ?></h1>
      <p class="text-sm text-slate-500 mb-4"><i class="fa-solid fa-location-dot text-emerald-600 mr-1"></i><?= e($car['district']) ?> • Chủ xe: <strong><?= e($car['host_name']) ?></strong></p>
      <p class="text-sm text-slate-700 leading-relaxed mb-6"><?= e($car['description']) ?></p>

      <h3 class="font-bold text-sm uppercase text-slate-500 mb-3">Thông số kỹ thuật</h3>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs mb-6">
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100"><span class="block text-slate-400">Số ghế</span><strong class="text-sm"><?= (int)$car['seats'] ?> chỗ</strong></div>
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100"><span class="block text-slate-400">Hộp số</span><strong class="text-sm"><?= e($car['transmission']) ?></strong></div>
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100"><span class="block text-slate-400">Nhiên liệu</span><strong class="text-sm"><?= e($car['fuel']) ?></strong></div>
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100"><span class="block text-slate-400">Tiêu hao</span><strong class="text-sm"><?= e($car['consumption']) ?></strong></div>
      </div>

      <h3 class="font-bold text-sm uppercase text-slate-500 mb-3">Tính năng tiện nghi</h3>
      <div class="flex flex-wrap gap-2">
        <?php foreach (explode(',', $car['features']) as $feat): ?>
          <?php if (trim($feat)): ?>
            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-semibold"><i class="fa-solid fa-check mr-1"></i><?= e(trim($feat)) ?></span>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Booking Form -->
    <div class="lg:col-span-5">
      <?php if (current_user() === null): ?>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-lg text-center">
          <h2 class="text-lg font-extrabold text-slate-900">Đăng nhập để đặt xe</h2>
          <p class="text-sm text-slate-500 mt-2 mb-5">Đơn đặt xe sẽ được lưu vào tài khoản của bạn.</p>
          <a href="<?= base_url('/login') ?>" class="inline-block px-5 py-3 bg-emerald-600 text-white font-bold rounded-xl">Đăng nhập</a>
          <a href="<?= base_url('/register') ?>" class="inline-block ml-2 px-5 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl">Đăng ký</a>
        </div>
      <?php else: ?>
      <form method="POST" action="<?= base_url('/booking/store') ?>" id="booking-form" data-price="<?= (int)$car['price_per_day'] ?>" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-lg space-y-4 text-sm">
        <?= csrf_field() ?>
        <input type="hidden" name="car_id" value="<?= (int)$car['id'] ?>" />
        <div class="flex items-baseline justify-between border-b border-slate-100 pb-4">
          <div>
            <span class="text-2xl font-extrabold text-emerald-600"><?= format_vnd((int)$car['price_per_day']) ?></span>
            <span class="text-slate-500 text-xs">/ ngày</span>
          </div>
          <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">Sẵn sàng giao xe</span>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-600 mb-1">Nhận xe *</label>
            <input type="datetime-local" name="pickup_date" id="pickup_date" required value="<?= date('Y-m-d\TH:00', strtotime('+1 day')) ?>" class="w-full h-10 px-2.5 rounded-xl border border-slate-200 text-xs" />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-600 mb-1">Trả xe *</label>
            <input type="datetime-local" name="return_date" id="return_date" required value="<?= date('Y-m-d\TH:00', strtotime('+3 days')) ?>" class="w-full h-10 px-2.5 rounded-xl border border-slate-200 text-xs" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-600 mb-1">Họ và tên khách thuê *</label>
          <input type="text" name="customer_name" required value="<?= e(current_user()['name']) ?>" placeholder="Nguyễn Văn A" class="w-full h-10 px-3 rounded-xl border border-slate-200" />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-600 mb-1">Số điện thoại *</label>
            <input type="tel" name="customer_phone" required placeholder="0912345678" class="w-full h-10 px-3 rounded-xl border border-slate-200" />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-600 mb-1">Số GPLX *</label>
            <input type="text" name="driver_license" required placeholder="790123456789" class="w-full h-10 px-3 rounded-xl border border-slate-200" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-600 mb-1">Hình thức nhận xe</label>
          <select name="delivery_type" id="delivery_type" class="w-full h-10 px-3 rounded-xl border border-slate-200">
            <option value="pickup">Nhận tại vị trí xe (Miễn phí)</option>
            <option value="delivery">Giao xe tận nơi nội thành (+100.000đ)</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-600 mb-1">Mã khuyến mãi (CHAO2026 / GIAM10)</label>
          <input type="text" name="promo_code" id="promo_code" placeholder="Nhập mã giảm giá nếu có" class="w-full h-10 px-3 rounded-xl border border-slate-200 uppercase" />
        </div>

        <div id="live-summary" class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-1.5 text-xs">
          <div class="flex justify-between"><span>Thời gian thuê:</span><strong id="sum-days">2 ngày</strong></div>
          <div class="flex justify-between"><span>Tiền thuê xe:</span><strong id="sum-base"><?= format_vnd($car['price_per_day'] * 2) ?></strong></div>
          <div class="flex justify-between"><span>Bảo hiểm chuyến đi (95k/ngày):</span><strong id="sum-ins">190.000đ</strong></div>
          <div class="flex justify-between"><span>Phí giao nhận:</span><strong id="sum-del">0đ</strong></div>
          <div class="flex justify-between text-base font-extrabold text-slate-900 pt-2 border-t border-slate-200">
            <span>Tổng thanh toán dự kiến:</span>
            <span id="sum-total" class="text-emerald-600"><?= format_vnd($car['price_per_day'] * 2 + 190000) ?></span>
          </div>
        </div>

        <button type="submit" class="w-full h-12 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-md transition">
          Xác nhận đặt xe ngay
        </button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>
