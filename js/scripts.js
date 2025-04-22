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
document.addEventListener("DOMContentLoaded", function () {
  // Setup dropdown toggles for all submenu items (both mobile and main nav)
  setupSubMenuToggles();

  // Function to handle submenu toggles
  function setupSubMenuToggles() {
    const dropdownToggles = document.querySelectorAll(".dropdown-toggle");

    dropdownToggles.forEach(function (toggle) {
      toggle.addEventListener("click", function (e) {
        e.preventDefault();

        // Find the parent list item
        const parent = this.parentNode;

        // Find the submenu (next element after the toggle button)
        const subMenu = parent.querySelector(".sub-menu");

        if (subMenu) {
          // Toggle submenu visibility
          subMenu.classList.toggle("is-active");

          // Toggle aria-expanded
          const isExpanded = this.getAttribute("aria-expanded") === "true";
          this.setAttribute("aria-expanded", !isExpanded);
        }
      });
    });
  }

  // Desktop hover functionality (optional)
  if (window.innerWidth > 768) {
    // Add breakpoint check
    setupDesktopHoverEffect();
  }

  function setupDesktopHoverEffect() {
    const menuItemsWithChildren = document.querySelectorAll(
      ".top-menu .menu-item-has-children"
    );

    menuItemsWithChildren.forEach(function (item) {
      item.addEventListener("mouseenter", function () {
        const subMenu = this.querySelector(".sub-menu");
        if (subMenu) {
          subMenu.classList.add("is-active");
        }
      });

      item.addEventListener("mouseleave", function () {
        const subMenu = this.querySelector(".sub-menu");
        if (subMenu) {
          subMenu.classList.remove("is-active");
        }
      });
    });
  }

  // Handle resize events to toggle between hover and click behavior
  window.addEventListener("resize", function () {
    if (window.innerWidth > 768) {
      setupDesktopHoverEffect();
    }
  });
});

// scroller

document.addEventListener("DOMContentLoaded", function () {
  const carousel = document.querySelector(".carousel_items");

  // Clone items for truly infinite scrolling
  const items = carousel.querySelectorAll("figure");

  // Create copies of existing items and append them
  items.forEach((item) => {
    const clone = item.cloneNode(true);
    carousel.appendChild(clone);
  });

  // Calculate the correct animation distance
  function updateScrollAnimation() {
    const itemsWidth = Array.from(items).reduce((width, item) => {
      return (
        width +
        item.offsetWidth +
        parseInt(getComputedStyle(item).marginLeft) +
        parseInt(getComputedStyle(item).marginRight)
      );
    }, 0);

    // Create a new style element with the correct animation
    const style = document.createElement("style");
    style.textContent = `
      @keyframes scroll {
        0% {
          transform: translateX(0);
        }
        100% {
          transform: translateX(-${itemsWidth}px);
        }
      }
    `;

    // Replace the old animation or add the new one
    document.head.appendChild(style);

    // Update animation
    carousel.style.animation = "none";
    carousel.offsetHeight; // Trigger reflow
    carousel.style.animation = `scroll ${
      30 + itemsWidth / 50
    }s linear infinite`;
  }

  // Run initial calculation
  updateScrollAnimation();

  // Recalculate on window resize
  window.addEventListener("resize", updateScrollAnimation);

  // Handle visibility change (prevents animation issues when tab is inactive)
  document.addEventListener("visibilitychange", function () {
    if (document.visibilityState === "visible") {
      carousel.style.animationPlayState = "running";
    } else {
      carousel.style.animationPlayState = "paused";
    }
  });
});
