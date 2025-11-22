/* ===========================
   FILE: pass.js
   Bảo vệ trang bằng mật khẩu
   Dùng chung cho ctrl.html & gt.html
   =========================== */

const ADMIN_PASS = "08231639";   // 🔐 ĐỔI MẬT KHẨU Ở ĐÂY

// ====== TẠO OVERLAY ======
const overlay = document.createElement("div");
overlay.style.position = "fixed";
overlay.style.inset = "0";
overlay.style.background = "#f8fafc";
overlay.style.display = "flex";
overlay.style.alignItems = "center";
overlay.style.justifyContent = "center";
overlay.style.zIndex = "999999";
overlay.style.fontFamily = "system-ui, sans-serif";

// Hộp chứa input
const box = document.createElement("div");
box.style.background = "#ffffff";
box.style.padding = "24px 26px 20px";
box.style.borderRadius = "20px";
box.style.width = "320px";
box.style.textAlign = "center";
box.style.boxShadow = "0 20px 45px rgba(0,0,0,0.15)";
box.style.border = "1px solid #e2e8f0";

// Title
const title = document.createElement("div");
title.style.fontSize = "18px";
title.style.fontWeight = "700";
title.style.marginBottom = "4px";
title.innerText = "Mật khẩu quản trị";

// Sub
const sub = document.createElement("div");
sub.style.fontSize = "13px";
sub.style.color = "#64748b";
sub.style.marginBottom = "14px";
sub.innerText = "Nhập mật khẩu để mở trang.";

// Input
const input = document.createElement("input");
input.type = "password";
input.placeholder = "Nhập mật khẩu...";
input.style.width = "100%";
input.style.padding = "8px 10px";
input.style.border = "1px solid #cbd5e1";
input.style.borderRadius = "6px";
input.style.marginBottom = "8px";
input.style.fontSize = "14px";

// Button
const button = document.createElement("button");
button.innerText = "Đăng nhập";
button.style.width = "100%";
button.style.padding = "8px";
button.style.background = "#2563eb";
button.style.color = "white";
button.style.border = "none";
button.style.borderRadius = "6px";
button.style.fontSize = "14px";
button.style.cursor = "pointer";

// Msg
const msg = document.createElement("div");
msg.style.marginTop = "6px";
msg.style.fontSize = "12px";
msg.style.height = "18px";
msg.style.color = "#dc2626";

// Append
box.appendChild(title);
box.appendChild(sub);
box.appendChild(input);
box.appendChild(button);
box.appendChild(msg);
overlay.appendChild(box);
document.body.appendChild(overlay);


// ====== CHECK PASS ======
function checkPass() {
  const val = input.value.trim();
  if (val === ADMIN_PASS) {
    overlay.remove();
    if (typeof window.initInner === "function") {
      window.initInner();
    }
  } else {
    msg.innerText = "Sai mật khẩu!";
    input.focus();
    input.select();
  }
}

button.onclick = checkPass;
input.onkeydown = (e) => {
  if (e.key === "Enter") checkPass();
};

// Chặn người xem trang trước khi nhập mật khẩu
document.addEventListener("DOMContentLoaded", () => {
  document.body.style.overflow = "hidden";
});

overlay.addEventListener("removed", () => {
  document.body.style.overflow = "auto";
});
