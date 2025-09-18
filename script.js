// Modern Navigation Script
let menu = document.querySelector("#menu-btn");
let navbar = document.querySelector(".header .navbar");
let header = document.querySelector(".header");

// Mobile menu toggle
menu.onclick = () => {
  menu.classList.toggle("fa-times");
  navbar.classList.toggle("active");
  document.body.classList.toggle("menu-open");
};

// Close mobile menu when clicking on a link
document.querySelectorAll(".navbar a").forEach((link) => {
  link.addEventListener("click", () => {
    navbar.classList.remove("active");
    menu.classList.remove("fa-times");
    document.body.classList.remove("menu-open");
  });
});

// Header scroll effect
window.addEventListener("scroll", () => {
  if (window.scrollY > 100) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});

// Active page highlighting
const currentPage = window.location.pathname.split("/").pop();
document.querySelectorAll(".navbar a").forEach((link) => {
  if (link.getAttribute("href") === currentPage) {
    link.classList.add("active");
  }
});

window.swiper = new Swiper(".home-slider", {
  loop: true,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  autoplay: {
    delay: 5000,
    disableOnInteraction: false,
  },
});

document.querySelectorAll(".rating-stars input").forEach((star) => {
  star.addEventListener("change", function () {
    console.log("Rating selected:", this.value);
  });
});

// User avatar dropdown functionality
function toggleDropdown() {
  const dropdown = document.getElementById("userDropdown");
  const avatar = document.querySelector(".user-avatar");

  if (dropdown.classList.contains("show")) {
    dropdown.classList.remove("show");
    avatar.classList.remove("active");
  } else {
    dropdown.classList.add("show");
    avatar.classList.add("active");
  }
}

// Close dropdown when clicking outside
document.addEventListener("click", function (event) {
  const dropdown = document.getElementById("userDropdown");
  const avatar = document.querySelector(".user-avatar");

  if (dropdown && !event.target.closest(".user-avatar-dropdown")) {
    dropdown.classList.remove("show");
    avatar.classList.remove("active");
  }
});

// Close dropdown on escape key
document.addEventListener("keydown", function (event) {
  if (event.key === "Escape") {
    const dropdown = document.getElementById("userDropdown");
    const avatar = document.querySelector(".user-avatar");

    if (dropdown && dropdown.classList.contains("show")) {
      dropdown.classList.remove("show");
      avatar.classList.remove("active");
    }
  }
});
