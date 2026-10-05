/* =========================================================
   STUDENTHUB JAVASCRIPT
   ========================================================= */

console.log("StudentHub JavaScript loaded successfully.");

document.addEventListener("DOMContentLoaded", function () {

    /* =========================================================
       THEME TOGGLE
       ========================================================= */

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

        const isDark =
            root.getAttribute("data-theme") === "dark";

        themeToggle.textContent =
            isDark ? "☀️" : "🌙";

        themeToggle.setAttribute(
            "aria-label",
            isDark
                ? "Switch to light mode"
                : "Switch to dark mode"
        );

        themeToggle.setAttribute(
            "title",
            isDark
                ? "Switch to light mode"
                : "Switch to dark mode"
        );
    }

    updateThemeButton();


    if (themeToggle) {

        themeToggle.addEventListener(
            "click",
            function () {

                const nextTheme =
                    root.getAttribute("data-theme") === "dark"
                        ? "light"
                        : "dark";

                root.setAttribute(
                    "data-theme",
                    nextTheme
                );

                localStorage.setItem(
                    "studenthub-theme",
                    nextTheme
                );

                updateThemeButton();

            }
        );

    }


    /* =========================================================
       HERO HEADING CHANGE
       ========================================================= */

    const heading =
        document.getElementById("hero-heading");

    const headingChangeButton =
        document.getElementById("heading-change-btn");


    if (heading && headingChangeButton) {

        headingChangeButton.addEventListener(
            "click",
            function () {

                heading.innerHTML =
                    "Learn. <span>Connect. Grow.</span>";

            }
        );

    }


    /* =========================================================
       FAQ ACCORDION
       ========================================================= */

    document
        .querySelectorAll(".faq-question")
        .forEach(function (question) {

            question.addEventListener(
                "click",
                function () {

                    const faqItem =
                        question.closest(".faq-item");

                    const isCurrentlyOpen =
                        faqItem.classList.contains("open");


                    document
                        .querySelectorAll(".faq-item")
                        .forEach(function (item) {

                            item.classList.remove("open");

                            item
                                .querySelector(".faq-question")
                                ?.setAttribute(
                                    "aria-expanded",
                                    "false"
                                );

                            const itemIcon =
                                item.querySelector(".faq-icon");

                            if (itemIcon) {
                                itemIcon.textContent = "+";
                            }

                        });


                    if (!isCurrentlyOpen) {

                        faqItem.classList.add("open");

                        question.setAttribute(
                            "aria-expanded",
                            "true"
                        );

                        const icon =
                            question.querySelector(".faq-icon");

                        if (icon) {
                            icon.textContent = "−";
                        }

                    }

                }
            );

        });


    /* =========================================================
       CAMPUS IMAGE SLIDER
       ========================================================= */

    const slides =
        document.querySelectorAll(".slide");

    const prevButton =
        document.querySelector(".slider-prev");

    const nextButton =
        document.querySelector(".slider-next");

    const dots =
        document.querySelectorAll(".slider-dot");


    let currentSlide = 0;

    let sliderInterval = null;


    function showSlide(index) {

        if (slides.length === 0) {
            return;
        }


        if (index >= slides.length) {

            currentSlide = 0;

        } else if (index < 0) {

            currentSlide = slides.length - 1;

        } else {

            currentSlide = index;

        }


        slides.forEach(function (slide, index) {

            slide.classList.toggle(
                "active",
                index === currentSlide
            );

        });


        dots.forEach(function (dot, index) {

            const isActive =
                index === currentSlide;


            dot.classList.toggle(
                "active",
                isActive
            );


            dot.setAttribute(
                "aria-current",
                isActive
                    ? "true"
                    : "false"
            );

        });

    }


    function nextSlide() {

        showSlide(
            currentSlide + 1
        );

    }


    function previousSlide() {

        showSlide(
            currentSlide - 1
        );

    }


    function startSlider() {

        clearInterval(
            sliderInterval
        );

        sliderInterval =
            setInterval(
                nextSlide,
                5000
            );

    }


    function resetSlider() {

        clearInterval(
            sliderInterval
        );

        startSlider();

    }


    if (slides.length > 0) {

        /* Show first slide */
        showSlide(0);


        /* Next button */
        if (nextButton) {

            nextButton.addEventListener(
                "click",
                function () {

                    nextSlide();

                    resetSlider();

                }
            );

        }


        /* Previous button */
        if (prevButton) {

            prevButton.addEventListener(
                "click",
                function () {

                    previousSlide();

                    resetSlider();

                }
            );

        }


        /* Indicator buttons */
        dots.forEach(function (dot, index) {

            dot.addEventListener(
                "click",
                function () {

                    showSlide(index);

                    resetSlider();

                }
            );

        });


        /* Automatic sliding */
        startSlider();

    }


    /* =========================================================
       PRACTICAL 6: FETCHED DATA PAGES
       ========================================================= */

    async function loadJSON(path, errorMessage) {
        const response = await fetch(path);

        if (!response.ok) {
            throw new Error(errorMessage + " HTTP status: " + response.status);
        }

        return response.json();
    }

    function createTextElement(tagName, text, className) {
        const element = document.createElement(tagName);
        element.textContent = text;

        if (className) {
            element.className = className;
        }

        return element;
    }

    function initialiseDataPage(config) {
        const page = document.getElementById(config.pageId);

        if (!page) {
            return;
        }

        const list = document.getElementById(config.listId);
        const status = document.getElementById(config.statusId);
        const pagination = document.getElementById(config.paginationId);
        const previous = document.getElementById(config.previousId);
        const next = document.getElementById(config.nextId);
        const pageInfo = document.getElementById(config.pageInfoId);
        const controls = config.controls.map(function (id) {
            return document.getElementById(id);
        });
        const itemsPerPage = 5;
        let currentPage = 1;
        let allItems = [];

        function filteredAndSortedItems() {
            let items = allItems.filter(config.matches);
            const sortValue = document.getElementById(config.sortId).value;

            items.sort(function (first, second) {
                return config.compare(first, second, sortValue);
            });

            return items;
        }

        function render() {
            const items = filteredAndSortedItems();
            const totalPages = Math.max(1, Math.ceil(items.length / itemsPerPage));

            currentPage = Math.min(currentPage, totalPages);
            const startIndex = (currentPage - 1) * itemsPerPage;
            const pageItems = items.slice(startIndex, startIndex + itemsPerPage);

            list.replaceChildren();

            if (items.length === 0) {
                status.textContent = config.emptyMessage;
                pagination.hidden = true;
                return;
            }

            status.textContent = "";
            pageItems.forEach(function (item) {
                list.appendChild(config.createCard(item));
            });

            pagination.hidden = false;
            previous.disabled = currentPage === 1;
            next.disabled = currentPage === totalPages;
            pageInfo.textContent = "Page " + currentPage + " of " + totalPages;
        }

        controls.forEach(function (control) {
            control.addEventListener("input", function () {
                currentPage = 1;
                render();
            });

            control.addEventListener("change", function () {
                currentPage = 1;
                render();
            });
        });

        previous.addEventListener("click", function () {
            if (currentPage > 1) {
                currentPage -= 1;
                render();
            }
        });

        next.addEventListener("click", function () {
            const totalPages = Math.max(1, Math.ceil(filteredAndSortedItems().length / itemsPerPage));

            if (currentPage < totalPages) {
                currentPage += 1;
                render();
            }
        });

        loadJSON(config.dataPath, config.errorMessage)
            .then(function (items) {
                allItems = items;
                render();
            })
            .catch(function (error) {
                console.error(error);
                status.textContent = config.userErrorMessage;
                list.replaceChildren();
                pagination.hidden = true;
            });
    }

    initialiseDataPage({
        pageId: "events-page",
        listId: "event-list",
        statusId: "events-status",
        paginationId: "events-pagination",
        previousId: "events-previous",
        nextId: "events-next",
        pageInfoId: "events-page-info",
        controls: ["event-search", "event-category", "event-sort"],
        sortId: "event-sort",
        dataPath: "../data/events.json",
        errorMessage: "Unable to load events.",
        userErrorMessage: "Sorry, events could not be loaded.",
        emptyMessage: "No events found.",
        matches: function (event) {
            const search = document.getElementById("event-search").value.toLowerCase();
            const category = document.getElementById("event-category").value;

            return event.title.toLowerCase().includes(search) &&
                (category === "" || event.category === category);
        },
        compare: function (first, second, sortValue) {
            if (sortValue === "title-asc") return first.title.localeCompare(second.title);
            if (sortValue === "title-desc") return second.title.localeCompare(first.title);
            if (sortValue === "date-asc") return new Date(first.date) - new Date(second.date);
            if (sortValue === "date-desc") return new Date(second.date) - new Date(first.date);
            return 0;
        },
        createCard: function (event) {
            const card = document.createElement("article");
            card.className = "data-card";
            card.appendChild(createTextElement("p", event.category, "data-tag"));
            card.appendChild(createTextElement("h3", event.title));
            card.appendChild(createTextElement("p", new Date(event.date + "T00:00:00").toLocaleDateString("en-IN", { day: "numeric", month: "long", year: "numeric" }), "data-meta"));
            card.appendChild(createTextElement("p", event.location, "data-meta"));
            return card;
        }
    });

    initialiseDataPage({
        pageId: "students-page",
        listId: "student-list",
        statusId: "students-status",
        paginationId: "students-pagination",
        previousId: "students-previous",
        nextId: "students-next",
        pageInfoId: "students-page-info",
        controls: ["student-search", "student-course", "student-year", "student-sort"],
        sortId: "student-sort",
        dataPath: "../data/students.json",
        errorMessage: "Unable to load students.",
        userErrorMessage: "Sorry, students could not be loaded.",
        emptyMessage: "No students found.",
        matches: function (student) {
            const search = document.getElementById("student-search").value.toLowerCase();
            const course = document.getElementById("student-course").value;
            const year = document.getElementById("student-year").value;

            return student.name.toLowerCase().includes(search) &&
                (course === "" || student.course === course) &&
                (year === "" || student.year === Number(year));
        },
        compare: function (first, second, sortValue) {
            if (sortValue === "name-asc") return first.name.localeCompare(second.name);
            if (sortValue === "name-desc") return second.name.localeCompare(first.name);
            if (sortValue === "year-asc") return first.year - second.year;
            if (sortValue === "year-desc") return second.year - first.year;
            return 0;
        },
        createCard: function (student) {
            const card = document.createElement("article");
            card.className = "data-card";
            card.appendChild(createTextElement("h3", student.name));
            card.appendChild(createTextElement("p", student.email, "data-email"));
            card.appendChild(createTextElement("p", student.course, "data-meta"));
            card.appendChild(createTextElement("p", "Year " + student.year, "data-tag"));
            return card;
        }
    });

    initialiseDataPage({
        pageId: "faqs-page",
        listId: "faq-list",
        statusId: "faqs-status",
        paginationId: "faqs-pagination",
        previousId: "faqs-previous",
        nextId: "faqs-next",
        pageInfoId: "faqs-page-info",
        controls: ["faq-search", "faq-sort"],
        sortId: "faq-sort",
        dataPath: "../data/faqs.json",
        errorMessage: "Unable to load FAQs.",
        userErrorMessage: "Sorry, FAQs could not be loaded.",
        emptyMessage: "No FAQs found.",
        matches: function (faq) {
            return faq.question.toLowerCase().includes(document.getElementById("faq-search").value.toLowerCase());
        },
        compare: function (first, second, sortValue) {
            if (sortValue === "question-asc") return first.question.localeCompare(second.question);
            if (sortValue === "question-desc") return second.question.localeCompare(first.question);
            return 0;
        },
        createCard: function (faq) {
            const card = document.createElement("article");
            card.className = "faq-data-card";
            card.appendChild(createTextElement("h3", faq.question));
            card.appendChild(createTextElement("p", faq.answer));
            return card;
        }
    });

});
