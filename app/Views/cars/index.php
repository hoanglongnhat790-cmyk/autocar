<!-- Hero & Thanh tìm kiếm cơ bản -->
<section class="bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-900 text-white py-10">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mb-6">
      <h1 class="text-3xl font-extrabold mb-2">Thuê xe tự lái chuyên nghiệp trên toàn quốc</h1>
      <p class="text-emerald-100 text-sm">Hơn 500+ xe đời mới sẵn sàng giao tận nơi.</p>
    </div>
  </div>
</section>

<!-- Main Content Layout (Sidebar Bộ lọc + Danh sách xe) -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <form method="GET" action="<?= base_url('/') ?>" id="filter-form" class="flex flex-col lg:flex-row gap-8">
    
    <!-- SIDEBAR BỘ LỌC NÂNG CAO -->
    <aside class="w-full lg:w-80 shrink-0 bg-white p-6 rounded-2xl border border-slate-200 h-fit space-y-6 shadow-sm">
      <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <h2 class="font-bold text-base text-slate-900 flex items-center gap-2">
          <i class="fa-solid fa-sliders text-emerald-600"></i> Bộ lọc nâng cao
        </h2>
        <a href="<?= base_url('/') ?>" class="text-xs font-semibold text-emerald-600 hover:underline">Đặt lại</a>
      </div>

      <!-- Địa điểm ẩn giữ trạng thái -->
      <input type="hidden" name="city" value="<?= e($filters['city']) ?>">
      <input type="hidden" name="category" value="<?= e($filters['category']) ?>">

      <!-- HÃNG XE (Checkbox dạng lưới 2 cột) -->
      <div>
        <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-3">Hãng xe</label>
        <div class="grid grid-cols-2 gap-2.5 text-sm">
          <?php 
          $brandList = ['VinFast', 'Mazda', 'Toyota', 'Hyundai', 'Mercedes', 'Ford', 'Kia'];
          foreach ($brandList as $b): 
            $isChecked = in_array($b, $filters['brands'] ?? []) ? 'checked' : '';
          ?>
            <label class="flex items-center gap-2.5 cursor-pointer text-slate-700">
              <input type="checkbox" name="brands[]" value="<?= $b ?>" <?= $isChecked ?> onchange="this.form.submit()" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
              <?= $b ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- GIÁ TỐI ĐA / NGÀY -->
<div class="pt-4 border-t border-slate-100">
  <div class="flex justify-between items-center text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2">
    <span>Giá tối đa / ngày</span>
    <span id="price-label" class="text-emerald-600 font-bold text-sm"><?= number_format($filters['max_price'], 0, ',', '.') ?>đ</span>
  </div>
  <input type="range" name="max_price" min="500000" max="3000000" step="100000" value="<?= $filters['max_price'] ?>" 
         oninput="document.getElementById('price-label').textContent = Number(this.value).toLocaleString('vi-VN') + 'đ'"
         class="w-full accent-emerald-600 cursor-pointer mb-2" />
  
  <!-- Thêm nút bấm áp dụng lọc giá để chắc chắn form được submit -->
  <button type="submit" class="w-full py-1.5 bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-700 text-xs font-bold rounded-lg transition">
    Lọc theo mức giá này
  </button>
  
  <div class="flex justify-between text-[11px] text-slate-400 mt-1">
    <span>500k</span>
    <span>1tr</span>
    <span>1.5tr</span>
    <span>2tr</span>
    <span>2.5tr</span>
    <span>3tr+</span>
  </div>
</div>
      </div>

      <!-- NHIÊN LIỆU (Dạng Pill Button) -->
      <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-3">Nhiên liệu</label>
        <div class="flex flex-wrap gap-2">
          <?php 
          $fuels = ['all' => 'Tất cả', 'Xăng' => 'Xăng', 'Điện' => 'Điện', 'Dầu' => 'Dầu'];
          foreach ($fuels as $val => $label): 
            $isActive = ($filters['fuel'] === $val) ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200';
          ?>
            <label class="px-3.5 py-1.5 rounded-full text-xs cursor-pointer transition <?= $isActive ?>">
              <input type="radio" name="fuel" value="<?= $val ?>" <?= ($filters['fuel'] === $val) ? 'checked' : '' ?> onchange="this.form.submit()" class="hidden">
              <?= $label ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- TÍNH NĂNG NỔI BẬT -->
      <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-3">Tính năng nổi bật</label>
        <div class="space-y-2.5 text-sm">
          <?php 
          $featureList = ['Cửa sổ trời', 'Camera 360', 'Cảm biến lùi', 'Bản đồ GPS'];
          foreach ($featureList as $feat): 
            $isCheckedFeat = in_array($feat, $filters['features'] ?? []) ? 'checked' : '';
          ?>
            <label class="flex items-center gap-2.5 cursor-pointer text-slate-700">
              <input type="checkbox" name="features[]" value="<?= $feat ?>" <?= $isCheckedFeat ?> onchange="this.form.submit()" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
              <?= $feat ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
    </aside>

    <!-- DANH SÁCH XE BÊN PHẢI -->
    <div class="flex-1">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-extrabold text-slate-900">Danh sách xe sẵn sàng (<?= count($cars) ?> xe)</h2>
      </div>

      <?php if (empty($cars)): ?>
        <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center text-slate-500 shadow-sm">
          Không tìm thấy xe phù hợp với bộ lọc nâng cao. <br>
          <a href="<?= base_url('/') ?>" class="inline-block mt-3 px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl">Xóa bộ lọc</a>
        </div>
      <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <?php foreach ($cars as $car): ?>
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl transition flex flex-col">
              <div class="relative h-48 bg-slate-200">
                <img src="<?= e($car['image']) ?>" alt="<?= e($car['name']) ?>" class="w-full h-full object-cover" />
                <span class="absolute bottom-3 left-3 px-2.5 py-1 rounded-lg bg-slate-900/80 text-white text-xs font-semibold backdrop-blur">
                  <?= e($car['transmission']) ?> • <?= e($car['fuel']) ?>
                </span>
              </div>
              <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                  <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                    <span><i class="fa-solid fa-location-dot text-emerald-600 mr-1"></i><?= e($car['district']) ?></span>
                    <span class="font-bold text-amber-500"><i class="fa-solid fa-star"></i> <?= e($car['rating']) ?> (<?= (int)$car['trips'] ?> chuyến)</span>
                  </div>
                  <h3 class="font-extrabold text-base text-slate-900 mb-2"><?= e($car['name']) ?></h3>
                  <div class="flex flex-wrap gap-1.5 mb-4">
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-xs"><?= (int)$car['seats'] ?> chỗ</span>
                    <?php foreach (array_slice(explode(',', $car['features']), 0, 2) as $feat): ?>
                      <?php if (trim($feat)): ?>
                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-xs"><?= e(trim($feat)) ?></span>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                  <div>
                    <span class="text-lg font-extrabold text-emerald-600"><?= format_vnd((int)$car['price_per_day']) ?></span>
                    <span class="text-xs text-slate-500">/ngày</span>
                  </div>
                  <a href="<?= base_url('/car/detail?id=' . $car['id']) ?>" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                    Chọn xe này
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </form>
</section>