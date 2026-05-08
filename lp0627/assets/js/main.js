const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

document.querySelectorAll('a[href^="#"]').forEach((link) => {
  link.addEventListener("click", (event) => {
    const target = document.querySelector(link.getAttribute("href"));

    if (!target) {
      return;
    }

    event.preventDefault();
    target.scrollIntoView({ behavior: prefersReducedMotion ? "auto" : "smooth" });
  });
});

const fixedCta = document.querySelector(".fixed-cta");
const heroButton = document.querySelector(".hero .button");

if (fixedCta && heroButton && "IntersectionObserver" in window) {
  const observer = new IntersectionObserver(
    ([entry]) => {
      fixedCta.classList.toggle("is-visible", !entry.isIntersecting);
    },
    { threshold: 0.15 }
  );

  observer.observe(heroButton);
} else if (fixedCta) {
  fixedCta.classList.add("is-visible");
}

const revealItems = document.querySelectorAll(".reveal");

if (prefersReducedMotion || !("IntersectionObserver" in window)) {
  revealItems.forEach((item) => item.classList.add("is-visible"));
} else {
  const revealObserver = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target);
      });
    },
    { rootMargin: "0px 0px -10% 0px", threshold: 0.1 }
  );

  revealItems.forEach((item) => revealObserver.observe(item));
}
