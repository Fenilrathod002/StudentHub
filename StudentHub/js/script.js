/* =========================================================
   STUDENTHUB JAVASCRIPT
   ========================================================= */

console.log("StudentHub JavaScript loaded successfully.");

document.addEventListener("DOMContentLoaded", function () {
    const themeToggle = document.getElementById("theme-toggle");
    const root = document.documentElement;
    const savedTheme = localStorage.getItem("studenthub-theme");

    root.setAttribute(
        "data-theme",
        savedTheme === "dark" ? "dark" : "light"
    );

    function updateThemeButton() {
        if (!themeToggle) {
            return;
        }

        const isDark = root.getAttribute("data-theme") === "dark";
        themeToggle.textContent = isDark ? "☀️" : "🌙";
        themeToggle.setAttribute(
            "aria-label",
            isDark ? "Switch to light mode" : "Switch to dark mode"
        );
        themeToggle.setAttribute(
            "title",
            isDark ? "Switch to light mode" : "Switch to dark mode"
        );
    }

    updateThemeButton();

    if (themeToggle) {
        themeToggle.addEventListener("click", function () {
            const nextTheme =
                root.getAttribute("data-theme") === "dark"
                    ? "light"
                    : "dark";

            root.setAttribute("data-theme", nextTheme);
            localStorage.setItem("studenthub-theme", nextTheme);
            updateThemeButton();
        });
    }

    const heading = document.getElementById("hero-heading");
    const headingChangeButton = document.getElementById("heading-change-btn");

    if (heading && headingChangeButton) {
        headingChangeButton.addEventListener("click", function () {
            heading.innerHTML = "Learn. <span>Connect. Grow.</span>";
        });
    }

    document.querySelectorAll(".faq-question").forEach(function (question) {
        question.addEventListener("click", function () {
            const faqItem = question.closest(".faq-item");
            const isCurrentlyOpen = faqItem.classList.contains("open");

            document.querySelectorAll(".faq-item").forEach(function (item) {
                item.classList.remove("open");
                item.querySelector(".faq-question")?.setAttribute(
                    "aria-expanded",
                    "false"
                );
                const itemIcon = item.querySelector(".faq-icon");
                if (itemIcon) {
                    itemIcon.textContent = "+";
                }
            });

            if (!isCurrentlyOpen) {
                faqItem.classList.add("open");
                question.setAttribute("aria-expanded", "true");
                const icon = question.querySelector(".faq-icon");
                if (icon) {
                    icon.textContent = "−";
                }
            }
        });
    });
});
