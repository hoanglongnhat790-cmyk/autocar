<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <h1 class="text-2xl font-extrabold text-slate-900 mb-6">Bảng điều khiển Quản trị & Chủ xe</h1>

  <!-- Stats -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-5 rounded-2xl border border-slate-200">
      <span class="text-xs font-bold uppercase text-slate-400">Tổng số xe đang quản lý</span>
      <div class="text-3xl font-extrabold text-slate-900 mt-1"><?= count($cars) ?> xe</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200">
      <span class="text-xs font-bold uppercase text-slate-400">Tổng đơn đặt xe</span>
      <div class="text-3xl font-extrabold text-emerald-600 mt-1"><?= count($bookings) ?> đơn</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200">
      <span class="text-xs font-bold uppercase text-slate-400">Doanh thu ước tính</span>
      <div class="text-3xl font-extrabold text-amber-600 mt-1"><?= format_vnd($revenue) ?></div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Add Car Form -->
    <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-200 h-fit">
      <h2 class="text-lg font-extrabold text-slate-900 mb-4">+ Thêm xe mới cho thuê</h2>
      <form method="POST" action="<?= base_url('/admin/car/store') ?>" class="space-y-3 text-sm">
        <div>
          <label class="block text-xs font-bold mb-1">Tên mẫu xe *</label>
          <input type="text" name="name" required placeholder="VD: Mazda CX-5 Premium 2025" class="w-full h-10 px-3 rounded-xl border border-slate-200" />
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold mb-1">Hãng xe</label>
            <select name="brand" class="w-full h-10 px-3 rounded-xl border border-slate-200">
              <option>VinFast</option><option>Mazda</option><option>Hyundai</option><option>Toyota</option><option>Kia</option><option>Ford</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold mb-1">Phân khúc</label>
            <select name="category" class="w-full h-10 px-3 rounded-xl border border-slate-200">
              <option>4 chỗ</option><option>5 chỗ</option><option>7 chỗ</option><option>Xe điện</option><option>Bán tải</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold mb-1">Nhiên liệu</label>
            <select name="fuel" class="w-full h-10 px-3 rounded-xl border border-slate-200">
              <option>Xăng</option><option>Dầu</option><option>Điện</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold mb-1">Giá thuê/ngày (VNĐ) *</label>
            <input type="number" name="price_per_day" required min="300000" step="50000" value="950000" class="w-full h-10 px-3 rounded-xl border border-slate-200" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold mb-1">Thành phố</label>
            <select name="city" class="w-full h-10 px-3 rounded-xl border border-slate-200">
              <option>Hà Nội</option><option>TP.HCM</option><option>Đà Nẵng</option><option>Phú Quốc</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold mb-1">Quận/Huyện giao xe *</label>
            <input type="text" name="district" required placeholder="VD: Cầu Giấy, Hà Nội" class="w-full h-10 px-3 rounded-xl border border-slate-200" />
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold mb-1">Tên chủ xe *</label>
          <input type="text" name="host_name" required value="VietCar Partner" class="w-full h-10 px-3 rounded-xl border border-slate-200" />
        </div>
        <div>
          <label class="block text-xs font-bold mb-1">URL hình ảnh xe</label>
          <input type="url" name="image" placeholder="https://images.unsplash.com/..." class="w-full h-10 px-3 rounded-xl border border-slate-200" />
        </div>
        <div>
          <label class="block text-xs font-bold mb-1">Mô tả chi tiết</label>
          <textarea name="description" rows="2" class="w-full p-3 rounded-xl border border-slate-200" placeholder="Xe đời mới, bảo dưỡng định kỳ..."></textarea>
        </div>
        <button type="submit" class="w-full h-11 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl">Lưu xe vào CSDL</button>
      </form>
    </div>

    <!-- Car Table -->
    <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200 overflow-x-auto">
      <h2 class="text-lg font-extrabold text-slate-900 mb-4">Danh sách xe trong Database</h2>
      <table class="w-full text-left text-sm border-collapse">
        <thead>
          <tr class="border-b border-slate-200 text-xs uppercase text-slate-500">
            <th class="py-3">Xe</th>
            <th class="py-3">Khu vực</th>
            <th class="py-3">Giá/ngày</th>
            <th class="py-3 text-right">Thao tác</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($cars as $c): ?>
            <tr>
              <td class="py-3 font-bold text-slate-900"><?= e($c['name']) ?></td>
              <td class="py-3 text-xs text-slate-600"><?= e($c['city']) ?></td>
              <td class="py-3 font-bold text-emerald-600"><?= format_vnd((int)$c['price_per_day']) ?></td>
              <td class="py-3 text-right">
                <form method="POST" action="<?= base_url('/admin/car/delete') ?>" onsubmit="return confirm('Xóa xe này khỏi CSDL?')">
                  <input type="hidden" name="car_id" value="<?= (int)$c['id'] ?>" />
                  <button type="submit" class="text-xs text-rose-600 hover:underline font-semibold">Xóa</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Nhúng thư viện bản đồ Leaflet miễn phí -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Khu vực Giám sát hành trình GPS -->
<div class="bg-white p-6 rounded-2xl border border-slate-200 mb-8">
  <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
    <div>
      <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
        <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
        Giám sát vị trí đội xe trực tuyến (GPS Tracking)
      </h2>
      <p class="text-xs text-slate-500">Tự động cập nhật tọa độ mỗi 3 giây • Bấm vào biểu tượng xe để xem tốc độ</p>
    </div>
    <div class="flex items-center gap-2">
      <button type="button" onclick="focusCity(21.0285, 105.8048, 12)" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 hover:bg-slate-200">Hà Nội</button>
      <button type="button" onclick="focusCity(16.0544, 108.2022, 12)" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 hover:bg-slate-200">Đà Nẵng</button>
      <button type="button" onclick="focusCity(10.7769, 106.7009, 12)" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 hover:bg-slate-200">TP.HCM</button>
      <button type="button" id="btn-sim" onclick="toggleSimulation()" class="px-3.5 py-1.5 text-xs font-bold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">
        ▶ Bật mô phỏng xe chạy
      </button>
    </div>
  </div>
  <div id="fleet-map" class="w-full h-96 rounded-xl border border-slate-200 z-10"></div>
</div>

<script>
  const map = L.map('fleet-map').setView([21.0285, 105.8048], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  const carIcon = L.divIcon({
    html: '<div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-lg border-2 border-white text-xs"><i class="fa-solid fa-car"></i></div>',
    className: '',
    iconSize: [32, 32],
    iconAnchor: [16, 16]
  });

  let markers = {};
  let simInterval = null;

  function focusCity(lat, lng, zoom) {
    map.setView([lat, lng], zoom);
  }

  async function fetchCarLocations() {
    const res = await fetch('<?= base_url('/api/cars/locations') ?>');
    const cars = await res.json();

    cars.forEach(car => {
      const lat = parseFloat(car.lat);
      const lng = parseFloat(car.lng);
      const popupHtml = `
        <div class="text-xs">
          <strong class="text-slate-900 text-sm">${car.name}</strong><br>
          <span>Khu vực: ${car.district}</span><br>
          <span>Tốc độ hiện tại: <strong class="text-emerald-600">${car.speed} km/h</strong></span>
        </div>
      `;

      if (markers[car.id]) {
        markers[car.id].setLatLng([lat, lng]);
        markers[car.id].setPopupContent(popupHtml);
      } else {
        markers[car.id] = L.marker([lat, lng], { icon: carIcon })
          .addTo(map)
          .bindPopup(popupHtml);
      }
    });
  }

  function toggleSimulation() {
    const btn = document.getElementById('btn-sim');
    if (simInterval) {
      clearInterval(simInterval);
      simInterval = null;
      btn.textContent = '▶ Bật mô phỏng xe chạy';
      btn.classList.replace('bg-rose-600', 'bg-emerald-600');
    } else {
      btn.textContent = '⏸ Đang mô phỏng (Dừng)';
      btn.classList.replace('bg-emerald-600', 'bg-rose-600');
      simInterval = setInterval(async () => {
        await fetch('<?= base_url('/api/cars/simulate') ?>', { method: 'POST' });
        await fetchCarLocations();
      }, 2500);
    }
  }

  fetchCarLocations();
</script>