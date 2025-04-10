const backdrop = document.querySelector(".backdrop");
const toggleButton = document.querySelector(".toggle-button");
const mobileNav = document.querySelector(".mobile-nav__container");

console.log(backdrop, toggleButton, mobileNav);

backdrop.addEventListener("click", function () {
  backdrop.classList.remove("open");
  mobileNav.classList.remove("open");
});

toggleButton.addEventListener("click", function () {
  backdrop.classList.toggle("open");
  mobileNav.classList.toggle("open");
});
