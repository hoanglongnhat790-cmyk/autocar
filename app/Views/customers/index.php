<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Quản lý khách hàng</h1>
      <p class="text-sm text-slate-500 mt-1">Danh sách khách hàng và trạng thái tài khoản liên kết.</p>
    </div>
    <a href="<?= base_url('/customers/create') ?>" class="px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700">
      <i class="fa-solid fa-plus mr-1"></i> Thêm khách hàng
    </a>
  </div>

  <form method="GET" action="<?= base_url('/customers') ?>" class="bg-white p-4 rounded-2xl border border-slate-200 mb-6 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-56">
      <label class="block text-xs font-bold text-slate-500 mb-1">Tìm kiếm</label>
      <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Họ tên, email, số điện thoại"
             class="w-full h-10 px-3 rounded-xl border border-slate-200 text-sm" />
    </div>
    <div>
      <label class="block text-xs font-bold text-slate-500 mb-1">Trạng thái</label>
      <select name="status" class="h-10 px-3 rounded-xl border border-slate-200 text-sm bg-white">
        <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>Tất cả</option>
        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
        <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
      </select>
    </div>
    <button type="submit" class="h-10 px-5 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-emerald-600">Lọc</button>
  </form>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-x-auto">
    <table class="w-full text-left text-sm border-collapse">
      <thead>
        <tr class="border-b border-slate-200 text-xs uppercase text-slate-500">
          <th class="py-3 px-4">Khách hàng</th>
          <th class="py-3 px-4">Liên hệ</th>
          <th class="py-3 px-4">Địa chỉ</th>
          <th class="py-3 px-4">Tài khoản</th>
          <th class="py-3 px-4">Trạng thái</th>
          <th class="py-3 px-4 text-right">Thao tác</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (!$customers): ?>
          <tr><td colspan="6" class="py-8 px-4 text-center text-slate-400">Không có khách hàng nào.</td></tr>
        <?php endif; ?>
        <?php foreach ($customers as $c): ?>
          <tr>
            <td class="py-3 px-4">
              <div class="font-bold text-slate-900"><?= e($c['full_name']) ?></div>
              <div class="text-xs text-slate-400">#<?= (int)$c['id'] ?> • CCCD: <?= e($c['identity_number'] ?: '—') ?></div>
            </td>
            <td class="py-3 px-4 text-xs text-slate-600">
              <div><?= e($c['email']) ?></div>
              <div><?= e($c['phone']) ?></div>
            </td>
            <td class="py-3 px-4 text-xs text-slate-600"><?= e($c['address'] ?: '—') ?></td>
            <td class="py-3 px-4 text-xs text-slate-600"><?= (int)$c['account_count'] ?> tài khoản</td>
            <td class="py-3 px-4">
              <span class="text-xs font-bold px-2 py-1 rounded-lg <?= $c['status'] === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                <?= $c['status'] === 'active' ? 'Hoạt động' : 'Ngưng' ?>
              </span>
            </td>
            <td class="py-3 px-4">
              <div class="flex justify-end gap-3 text-xs font-semibold">
                <a href="<?= base_url('/customers/edit?id=' . (int)$c['id']) ?>" class="text-indigo-600 hover:underline">Sửa</a>
                <form method="POST" action="<?= base_url('/customer/status') ?>">
                  <input type="hidden" name="id" value="<?= (int)$c['id'] ?>" />
                  <input type="hidden" name="status" value="<?= $c['status'] === 'active' ? 'inactive' : 'active' ?>" />
                  <button type="submit" class="text-amber-600 hover:underline"><?= $c['status'] === 'active' ? 'Ngưng' : 'Kích hoạt' ?></button>
                </form>
                <form method="POST" action="<?= base_url('/customer/delete') ?>" onsubmit="return confirm('Xóa khách hàng này?')">
                  <input type="hidden" name="id" value="<?= (int)$c['id'] ?>" />
                  <button type="submit" class="text-rose-600 hover:underline">Xóa</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
