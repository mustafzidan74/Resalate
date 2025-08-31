const menuToggle = document.getElementById("menuToggle");
const sidebar = document.getElementById("sidebar");
const closeSidebar = document.querySelectorAll("#closeSidebar");
const overlay = document.getElementById("overlay");
const overlayNavbarList = document.getElementById("overlay-navbar");

const openSidebar = () => {
  sidebar.classList.remove("-translate-x-full");
  overlay.classList.remove("hidden");
};

const closeSidebarFn = () => {
  sidebar?.classList.add("-translate-x-full");
  navbarList?.classList.add("right-[-100%]");
  navbarList?.classList.remove("right-0");
  overlay?.classList.add("hidden");
  overlayNavbarList?.classList.add("hidden");
};

menuToggle?.addEventListener("click", openSidebar);
closeSidebar.forEach((e) => {
  e.addEventListener("click", closeSidebarFn);
});
overlay?.addEventListener("click", closeSidebarFn);
overlayNavbarList?.addEventListener("click", closeSidebarFn);

// Navbar (mobile)
const navbarToggle = document.getElementById("navbarToggle");
const navbarList = document.getElementById("navbarList");

navbarToggle?.addEventListener("click", () => {
  navbarList?.classList.toggle("right-0");
  navbarList?.classList.toggle("right-[-100%]");
  overlayNavbarList?.classList.toggle("hidden");
});

const notifBtn = document.getElementById("notifBtn");
const notifMenu = document.getElementById("notifMenu");
const userBtn = document.getElementById("userBtn");
const userMenu = document.getElementById("userMenu");

// Toggle menus
notifBtn?.addEventListener("click", (e) => {
  e?.stopPropagation();
  notifMenu?.classList.toggle("hidden");
  userMenu?.classList.add("hidden"); // Close other menu
});

userBtn?.addEventListener("click", (e) => {
  e?.stopPropagation();
  userMenu?.classList.toggle("hidden");
  notifMenu?.classList.add("hidden"); // Close other menu
});

// Close when clicking outside
document?.addEventListener("click", () => {
  notifMenu?.classList.add("hidden");
  userMenu?.classList.add("hidden");
});

const userRegisterBtn = document.getElementById("userRegisterBtn");
const clientBtn = document.getElementById("clientBtn");
const userForm = document.getElementById("userForm");
const clientForm = document.getElementById("clientForm");
let currentForm = "user";

userRegisterBtn?.addEventListener("click", () => {
  if (currentForm === "user") {
    // Show user form
    userForm?.classList.remove(
      "translate-x-full",
      "opacity-0",
      "pointer-events-none"
    );
    userForm?.classList.add("translate-x-0", "opacity-100");

    // Hide client form
    clientForm.classList.add(
      "translate-x-full",
      "opacity-0",
      "pointer-events-none"
    );
    clientForm.classList.remove("translate-x-0", "opacity-100");
  }
});

clientBtn?.addEventListener("click", () => {
  if (currentForm === "user") {
    // Show client form
    clientForm.classList?.remove(
      "translate-x-full",
      "opacity-0",
      "pointer-events-none"
    );
    clientForm?.classList.add("translate-x-0", "opacity-100");

    // Hide user form
    userForm?.classList.add(
      "translate-x-full",
      "opacity-0",
      "pointer-events-none"
    );
    userForm?.classList.remove("translate-x-0", "opacity-100");
  }
});

// ----------------  OTP Input Handler ---------------- //
document.addEventListener("DOMContentLoaded", () => {
  const inputs = document.querySelectorAll(
    "#reset-password-code-verification .otp-input"
  );

  inputs.forEach((input, index) => {
    input?.addEventListener("input", () => {
      const value = input.value;

      if (value === "") {
        for (let i = index - 1; i >= 0; i--) {
          if (inputs[i].value !== "") {
            inputs[i].focus();
            break;
          }
        }
        return;
      }

      if (!/^\d$/.test(value)) {
        input.value = "";
        return;
      }

      if (index + 1 < inputs.length) {
        inputs[index + 1].disabled = false;
        inputs[index + 1].focus();
      }
    });

    input.addEventListener("keydown", (e) => {
      if (e.key === "Backspace" && input.value === "") {
        if (index > 0) {
          inputs[index - 1].focus();
          inputs[index - 1].value = "";
          inputs[index].disabled = true;
        }
      }
    });

    input.addEventListener("focus", () => {
      for (let i = 0; i < index; i++) {
        if (inputs[i].value === "") {
          inputs[i].focus();
          break;
        }
      }
    });
  });
});

const filterButtons = document.querySelectorAll(".filter-btn");
const cards = document.querySelectorAll(".card-box");

filterButtons?.forEach((button) => {
  button?.addEventListener("click", () => {
    const filter = button?.getAttribute("data-filter");

    cards?.forEach((card) => {
      const category = card?.getAttribute("data-category");

      if (filter === "all" || category === filter) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    });
  });
});
