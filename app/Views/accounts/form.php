<div class="max-w-2xl mx-auto px-4 py-10">
  <div class="mb-6">
    <a href="<?= base_url('/accounts') ?>" class="text-sm text-slate-500 hover:text-emerald-600">
      <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại danh sách
    </a>
    <h1 class="text-2xl font-extrabold text-slate-900 mt-3"><?= $account ? 'Cập nhật tài khoản #' . (int)$account['id'] : 'Thêm tài khoản mới' ?></h1>
  </div>

  <form method="POST" action="<?= base_url($account ? '/account/update' : '/account/store') ?>" class="bg-white p-6 rounded-2xl border border-slate-200 space-y-4">
    <?php if ($account): ?>
      <input type="hidden" name="id" value="<?= (int)$account['id'] ?>" />
    <?php endif; ?>

    <div>
      <label class="block text-xs font-bold text-slate-600 mb-1">Tên đăng nhập *</label>
      <input type="text" name="username" required maxlength="50" value="<?= e($old['username'] ?? '') ?>"
             class="w-full h-11 px-3 rounded-xl border border-slate-200" />
      <?php if (!empty($errors['username'])): ?><p class="text-xs text-rose-600 mt-1"><?= e($errors['username']) ?></p><?php endif; ?>
    </div>

    <div>
      <label class="block text-xs font-bold text-slate-600 mb-1">
        Mật khẩu <?= $account ? '(để trống nếu không đổi)' : '*' ?>
      </label>
      <input type="password" name="password" <?= $account ? '' : 'required' ?> minlength="8"
             class="w-full h-11 px-3 rounded-xl border border-slate-200" />
      <?php if (!empty($errors['password'])): ?><p class="text-xs text-rose-600 mt-1"><?= e($errors['password']) ?></p><?php endif; ?>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-bold text-slate-600 mb-1">Vai trò</label>
        <select name="role" class="w-full h-11 px-3 rounded-xl border border-slate-200 bg-white">
          <?php foreach (['admin' => 'Admin', 'staff' => 'Nhân viên', 'customer' => 'Khách hàng'] as $key => $label): ?>
            <option value="<?= $key ?>" <?= ($old['role'] ?? 'customer') === $key ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-600 mb-1">Trạng thái</label>
        <select name="status" class="w-full h-11 px-3 rounded-xl border border-slate-200 bg-white">
          <option value="active" <?= ($old['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Hoạt động</option>
          <option value="locked" <?= ($old['status'] ?? '') === 'locked' ? 'selected' : '' ?>>Bị khóa</option>
        </select>
      </div>
    </div>

    <div>
      <label class="block text-xs font-bold text-slate-600 mb-1">Khách hàng liên kết</label>
      <select name="customer_id" class="w-full h-11 px-3 rounded-xl border border-slate-200 bg-white">
        <option value="">Không liên kết</option>
        <?php foreach ($customers as $c): ?>
          <option value="<?= (int)$c['id'] ?>" <?= (string)($old['customer_id'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>>
            <?= e($c['full_name']) ?> — <?= e($c['email']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (!empty($errors['customer_id'])): ?><p class="text-xs text-rose-600 mt-1"><?= e($errors['customer_id']) ?></p><?php endif; ?>
    </div>

    <button type="submit" class="w-full h-11 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl">
      <?= $account ? 'Lưu thay đổi' : 'Tạo tài khoản' ?>
    </button>
  </form>
</div>
