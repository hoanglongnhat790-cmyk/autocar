<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Quản lý tài khoản</h1>
      <p class="text-sm text-slate-500 mt-1">Quản trị đăng nhập, vai trò và trạng thái tài khoản.</p>
    </div>
    <a href="<?= base_url('/accounts/create') ?>" class="px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700">
      <i class="fa-solid fa-plus mr-1"></i> Thêm tài khoản
    </a>
  </div>

  <form method="GET" action="<?= base_url('/accounts') ?>" class="bg-white p-4 rounded-2xl border border-slate-200 mb-6 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-56">
      <label class="block text-xs font-bold text-slate-500 mb-1">Tìm kiếm</label>
      <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Họ tên, email, khách hàng"
             class="w-full h-10 px-3 rounded-xl border border-slate-200 text-sm" />
    </div>
    <div>
      <label class="block text-xs font-bold text-slate-500 mb-1">Vai trò</label>
      <select name="role" class="h-10 px-3 rounded-xl border border-slate-200 text-sm bg-white">
        <option value="all" <?= $role === 'all' ? 'selected' : '' ?>>Tất cả</option>
        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
        <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>Người dùng</option>
      </select>
    </div>
    <div>
      <label class="block text-xs font-bold text-slate-500 mb-1">Trạng thái</label>
      <select name="status" class="h-10 px-3 rounded-xl border border-slate-200 text-sm bg-white">
        <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>Tất cả</option>
        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Hoạt động</option>
        <option value="locked" <?= $status === 'locked' ? 'selected' : '' ?>>Bị khóa</option>
      </select>
    </div>
    <button type="submit" class="h-10 px-5 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-emerald-600">Lọc</button>
  </form>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-x-auto">
    <table class="w-full text-left text-sm border-collapse">
      <thead>
        <tr class="border-b border-slate-200 text-xs uppercase text-slate-500">
          <th class="py-3 px-4">Tài khoản</th>
          <th class="py-3 px-4">Vai trò</th>
          <th class="py-3 px-4">Khách hàng liên kết</th>
          <th class="py-3 px-4">Đăng nhập cuối</th>
          <th class="py-3 px-4">Trạng thái</th>
          <th class="py-3 px-4 text-right">Thao tác</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (!$accounts): ?>
          <tr><td colspan="6" class="py-8 px-4 text-center text-slate-400">Không có tài khoản nào.</td></tr>
        <?php endif; ?>
        <?php foreach ($accounts as $a): ?>
          <tr>
            <td class="py-3 px-4">
              <div class="font-bold text-slate-900"><?= e($a['name']) ?></div>
              <div class="text-xs text-slate-400"><?= e($a['email']) ?></div>
            </td>
            <td class="py-3 px-4">
              <span class="text-xs font-bold px-2 py-1 rounded-lg bg-indigo-50 text-indigo-700"><?= e($a['role']) ?></span>
            </td>
            <td class="py-3 px-4 text-xs text-slate-600"><?= e($a['customer_name'] ?: '—') ?></td>
            <td class="py-3 px-4 text-xs text-slate-500"><?= e($a['last_login_at'] ?: 'Chưa đăng nhập') ?></td>
            <td class="py-3 px-4">
              <span class="text-xs font-bold px-2 py-1 rounded-lg <?= $a['status'] === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' ?>">
                <?= $a['status'] === 'active' ? 'Hoạt động' : 'Bị khóa' ?>
              </span>
            </td>
            <td class="py-3 px-4">
              <div class="flex justify-end gap-3 text-xs font-semibold">
                <a href="<?= base_url('/accounts/edit?id=' . (int)$a['id']) ?>" class="text-indigo-600 hover:underline">Sửa</a>
                <form method="POST" action="<?= base_url('/account/status') ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int)$a['id'] ?>" />
                  <input type="hidden" name="status" value="<?= $a['status'] === 'active' ? 'locked' : 'active' ?>" />
                  <button type="submit" class="text-amber-600 hover:underline"><?= $a['status'] === 'active' ? 'Khóa' : 'Mở khóa' ?></button>
                </form>
                <form method="POST" action="<?= base_url('/account/delete') ?>" onsubmit="return confirm('Xóa tài khoản này?')">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int)$a['id'] ?>" />
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
