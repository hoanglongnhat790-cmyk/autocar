document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("booking-form");
  if (!form) return;

  const pricePerDay = Number(form.dataset.price || 0);
  const pickupEl = document.getElementById("pickup_date");
  const returnEl = document.getElementById("return_date");
  const deliveryEl = document.getElementById("delivery_type");

  function updateSummary() {
    const p = new Date(pickupEl.value);
    const r = new Date(returnEl.value);
    let days = Math.ceil((r - p) / (1000 * 3600 * 24));
    if (isNaN(days) || days < 1) days = 1;

    const base = pricePerDay * days;
    const ins = 95000 * days;
    const del = deliveryEl.value === "delivery" ? 100000 : 0;
    const total = base + ins + del;

    document.getElementById("sum-days").textContent = days + " ngày";
    document.getElementById("sum-base").textContent = base.toLocaleString("vi-VN") + "đ";
    document.getElementById("sum-ins").textContent = ins.toLocaleString("vi-VN") + "đ";
    document.getElementById("sum-del").textContent = del.toLocaleString("vi-VN") + "đ";
    document.getElementById("sum-total").textContent = total.toLocaleString("vi-VN") + "đ";
  }

  pickupEl.addEventListener("change", updateSummary);
  returnEl.addEventListener("change", updateSummary);
  deliveryEl.addEventListener("change", updateSummary);
});
