<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Lịch sử đặt xe & Chuyến của tôi</h1>
      <p class="text-sm text-slate-500">Dữ liệu được lưu trữ trực tiếp trong bảng <code>bookings</code> của MySQL.</p>
    </div>
    <a href="<?= base_url('/') ?>" class="px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700">+ Đặt xe mới</a>
  </div>

  <?php if (empty($bookings)): ?>
    <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center text-slate-500">
      Chưa có đơn đặt xe nào trên hệ thống.
    </div>
  <?php else: ?>
    <div class="space-y-4">
      <?php foreach ($bookings as $b): ?>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <img src="<?= e($b['car_image']) ?>" class="w-24 h-20 rounded-xl object-cover shrink-0" />
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs font-bold">#<?= e($b['booking_code']) ?></span>
                <?php if ($b['status'] === 'confirmed'): ?>
                  <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-xs font-bold">Đã xác nhận</span>
                <?php else: ?>
                  <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 text-xs font-bold">Đã hủy</span>
                <?php endif; ?>
              </div>
              <h3 class="font-extrabold text-slate-900"><?= e($b['car_name']) ?></h3>
              <p class="text-xs text-slate-500 mt-0.5">Nhận: <?= e($b['pickup_date']) ?> → Trả: <?= e($b['return_date']) ?> (<?= (int)$b['rental_days'] ?> ngày)</p>
              <p class="text-xs text-slate-500">Khách: <strong><?= e($b['customer_name']) ?></strong> • SĐT: <?= e($b['customer_phone']) ?></p>
            </div>
          </div>
          <div class="text-left sm:text-right w-full sm:w-auto border-t sm:border-t-0 pt-3 sm:pt-0">
            <div class="text-lg font-extrabold text-emerald-600"><?= format_vnd((int)$b['total_amount']) ?></div>
            <?php if ((int)$b['discount_amount'] > 0): ?>
              <div class="text-xs text-emerald-700">Đã giảm: -<?= format_vnd((int)$b['discount_amount']) ?></div>
            <?php endif; ?>
            <?php if ($b['status'] === 'confirmed'): ?>
              <form method="POST" action="<?= base_url('/booking/cancel') ?>" class="mt-2" onsubmit="return confirm('Xác nhận hủy đơn đặt xe này?')">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>" />
                <button type="submit" class="px-3 py-1 rounded-lg border border-rose-200 text-rose-600 text-xs font-semibold hover:bg-rose-50">Hủy chuyến</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Nút dẫn tới biên bản bàn giao gắn trong danh sách chuyến đi -->
<a href="<?= base_url('/booking/inspection?booking_id=' . $booking['id']) ?>" 
   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 text-teal-700 hover:bg-teal-100 font-bold text-xs rounded-xl transition">
    <i class="fa-solid fa-clipboard-check"></i> Lập biên bản bàn giao xe
</a>
