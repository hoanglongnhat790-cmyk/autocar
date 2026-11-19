<div class="max-w-2xl mx-auto px-4 py-10">
  <div class="mb-6">
    <a href="<?= base_url('/customers') ?>" class="text-sm text-slate-500 hover:text-emerald-600">
      <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại danh sách
    </a>
    <h1 class="text-2xl font-extrabold text-slate-900 mt-3"><?= $customer ? 'Cập nhật khách hàng #' . (int)$customer['id'] : 'Thêm khách hàng mới' ?></h1>
  </div>

  <form method="POST" action="<?= base_url($customer ? '/customer/update' : '/customer/store') ?>" class="bg-white p-6 rounded-2xl border border-slate-200 space-y-4">
    <?= csrf_field() ?>
    <?php if ($customer): ?>
      <input type="hidden" name="id" value="<?= (int)$customer['id'] ?>" />
    <?php endif; ?>

    <div>
      <label class="block text-xs font-bold text-slate-600 mb-1">Họ tên *</label>
      <input type="text" name="full_name" required maxlength="100" value="<?= e($old['full_name'] ?? '') ?>"
             class="w-full h-11 px-3 rounded-xl border border-slate-200" />
      <?php if (!empty($errors['full_name'])): ?><p class="text-xs text-rose-600 mt-1"><?= e($errors['full_name']) ?></p><?php endif; ?>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-bold text-slate-600 mb-1">Email *</label>
        <input type="email" name="email" required maxlength="150" value="<?= e($old['email'] ?? '') ?>"
               class="w-full h-11 px-3 rounded-xl border border-slate-200" />
        <?php if (!empty($errors['email'])): ?><p class="text-xs text-rose-600 mt-1"><?= e($errors['email']) ?></p><?php endif; ?>
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-600 mb-1">Số điện thoại *</label>
        <input type="tel" name="phone" required value="<?= e($old['phone'] ?? '') ?>"
               class="w-full h-11 px-3 rounded-xl border border-slate-200" />
        <?php if (!empty($errors['phone'])): ?><p class="text-xs text-rose-600 mt-1"><?= e($errors['phone']) ?></p><?php endif; ?>
      </div>
    </div>

    <div>
      <label class="block text-xs font-bold text-slate-600 mb-1">Số CCCD/CMND</label>
      <input type="text" name="identity_number" maxlength="30" value="<?= e($old['identity_number'] ?? '') ?>"
             class="w-full h-11 px-3 rounded-xl border border-slate-200" />
      <?php if (!empty($errors['identity_number'])): ?><p class="text-xs text-rose-600 mt-1"><?= e($errors['identity_number']) ?></p><?php endif; ?>
    </div>

    <div>
      <label class="block text-xs font-bold text-slate-600 mb-1">Địa chỉ</label>
      <textarea name="address" rows="2" maxlength="255" class="w-full p-3 rounded-xl border border-slate-200"><?= e($old['address'] ?? '') ?></textarea>
      <?php if (!empty($errors['address'])): ?><p class="text-xs text-rose-600 mt-1"><?= e($errors['address']) ?></p><?php endif; ?>
    </div>

    <div>
      <label class="block text-xs font-bold text-slate-600 mb-1">Trạng thái</label>
      <select name="status" class="w-full h-11 px-3 rounded-xl border border-slate-200 bg-white">
        <option value="active" <?= ($old['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
        <option value="inactive" <?= ($old['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
      </select>
    </div>

    <button type="submit" class="w-full h-11 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl">
      <?= $customer ? 'Lưu thay đổi' : 'Thêm khách hàng' ?>
    </button>
  </form>
</div>
