// ==========================================
// 1. UTILS & CORE FUNCTIONS
// ==========================================
// ==========================================
// 2. UI & ANIMATIONS
// ==========================================
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if (target) target.scrollIntoView({ behavior: "smooth" });
  });
});

function animateCounters() {
  const counters = document.querySelectorAll(".stat-number");
  counters.forEach((counter) => {
    const target = parseInt(counter.getAttribute("data-target"));
    const increment = target / 125;
    let current = 0;
    const update = () => {
      if (current < target) {
        current += increment;
        counter.textContent = Math.floor(current);
        requestAnimationFrame(update);
      } else {
        counter.textContent = target;
      }
    };
    update();
  });
}

const statsSection = document.getElementById("stats");
if (statsSection) {
  const observer = new IntersectionObserver(
    (entries) => {
      if (entries.isIntersecting) {
        animateCounters();
        observer.unobserve(statsSection);
      }
    },
    { threshold: 0.5 },
  );
  observer.observe(statsSection);
}

// Mobile Menu Toggle
const menuToggle = document.getElementById("menuToggle");
const navLinks = document.getElementById("navLinks");
const menuOverlay = document.getElementById("menuOverlay");

if (menuToggle) {
  menuToggle.addEventListener("click", () => {
    navLinks.classList.toggle("active");
    menuOverlay.classList.toggle("active");

    // Ubah ikon bars menjadi times (X)
    const icon = menuToggle.querySelector("i");
    icon.classList.toggle("fa-bars");
    icon.classList.toggle("fa-times");
  });
}

if (menuOverlay) {
  menuOverlay.addEventListener("click", () => {
    navLinks.classList.remove("active");
    menuOverlay.classList.remove("active");
    const icon = menuToggle.querySelector("i");
    icon.classList.add("fa-bars");
    icon.classList.remove("fa-times");
  });
}

// ==========================================
// 3. ADMIN & LOGIN LOGIC (REMOVED)
// ==========================================
// ==========================================
// 4. DATA MANAGEMENT (Complaints & Storage)
// ==========================================
async function loadComplaints() {
  const container = document.getElementById("complaints-container");
  const filter = document.getElementById("filterCategoryAdmin")?.value || "";
  const startDate = document.getElementById("startDateAdmin")?.value || "";
  const endDate = document.getElementById("endDateAdmin")?.value || "";

  if (!container) return;
  container.innerHTML = "";

  try {
    const queryParams = new URLSearchParams({
      category: filter,
      start_date: startDate,
      end_date: endDate,
    });
    const response = await fetch(
      `complaints_api.php?${queryParams.toString()}`,
    );
    let complaints = await response.json();

    if (complaints.length === 0) {
      container.innerHTML = `<p style="text-align:center; color:#94a3b8;">Belum ada data.</p>`;
      return;
    }

    complaints.forEach((c) => {
      const card = document.createElement("div");
      card.className = `complaint-card ${c.category}`;
      card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
          <span class="complaint-category">${c.category}</span>
          ${
            document.body.classList.contains("admin-active")
              ? `
            <div class="admin-tools" style="display:flex; gap:10px;">
              <button onclick="printSingle('${c.id}')" style="border:none; background:none; color:var(--text-muted); cursor:pointer;"><i class="fas fa-print"></i></button>
              <button onclick="deleteComplaint('${c.id}')" style="border:none; background:none; color:#ef4444; cursor:pointer;"><i class="fas fa-trash"></i></button>
            </div>
          `
              : ""
          }
        </div>
        <h4>${c.name}</h4>
        <p>"${c.message}"</p>
        <div class="complaint-status-wrapper">
            <span class="complaint-status-badge ${c.status}">${c.status}</span>
        </div>
        <div class="complaint-footer">
          <small>${c.time}</small>
          ${
            document.body.classList.contains("admin-active")
              ? `
            <a href="https://wa.me/${c.whatsapp}?text=Halo ${c.name}, kami menanggapi laporan Anda: ${c.message}" target="_blank" class="whatsapp-reply-btn" style="display: inline-flex; text-decoration:none; font-size:0.8rem; padding:5px 10px;">
              <i class="fab fa-whatsapp"></i> Balas WA
            </a>
          `
              : ""
          }
        </div>
      `;
      container.appendChild(card);
    });
  } catch (err) {
    console.error("Gagal memuat data:", err);
  }
}

async function deleteComplaint(id) {
  if (!confirm("Hapus keluhan ini?")) return;
  const response = await fetch("complaints_api.php", {
    method: "POST",
    body: JSON.stringify({ action: "delete", id: id }),
  });
  const res = await response.json();
  if (res.success) {
    showToast("Keluhan dihapus");
    loadComplaints();
  }
}

async function exportToCSV() {
  const filter = document.getElementById("filterCategoryAdmin")?.value || "";
  const startDate = document.getElementById("startDateAdmin")?.value || "";
  const endDate = document.getElementById("endDateAdmin")?.value || "";

  const queryParams = new URLSearchParams({
    category: filter,
    start_date: startDate,
    end_date: endDate,
  });
  const response = await fetch(`complaints_api.php?${queryParams.toString()}`);
  const data = await response.json();

  let csv = "ID,Tanggal,Nama,Kategori,Pesan,Status\n";
  data.forEach((c) => {
    csv += `${c.id},"${c.time}","${c.name}","${c.category}","${c.message.replace(/"/g, '""')}","${c.status}"\n`;
  });

  const blob = new Blob([csv], { type: "text/csv" });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `Laporan_Keluhan_RW13_${new Date().toLocaleDateString()}.csv`;
  a.click();
}

function printSingle(id) {
  // Logika print sederhana: sembunyikan semua kartu kecuali yang dipilih, lalu print
  window.print();
}

// Event Listeners for Data
document
  .getElementById("complaintForm")
  ?.addEventListener("submit", function (e) {
    e.preventDefault();
    const data = {
      name: document.getElementById("formName").value,
      email: document.getElementById("formEmail").value,
      message: document.getElementById("formMessage").value,
      category: document.getElementById("formCategory").value,
      whatsapp: document.getElementById("formWhatsapp").value,
    };

    fetch("complaints_api.php", {
      method: "POST",
      body: JSON.stringify(data),
    }).then(() => {
      showToast("Keluhan berhasil dikirim");
      this.reset();
      loadComplaints();
    });
  });

document
  .getElementById("filterCategoryAdmin")
  ?.addEventListener("change", loadComplaints);
document
  .getElementById("startDateAdmin")
  ?.addEventListener("change", loadComplaints);
document
  .getElementById("endDateAdmin")
  ?.addEventListener("change", loadComplaints);

// ==========================================
// 5. INITIALIZATION
// ==========================================
window.addEventListener("load", () => {
  loadComplaints();
});

// Toast Helper
function showToast(message, type = "success") {
  const container = document.getElementById("toastContainer");
  if (!container) return;
  const toast = document.createElement("div");
  toast.className = `toast ${type}`;
  toast.innerHTML = `<span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => {
    toast.classList.add("fade-out");
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}
