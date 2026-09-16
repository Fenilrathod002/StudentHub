console.log("StudentHub Javascript Loaded Successfully.");
console.log("Welcome To The StudentHub.");
console.log("Practical-4 Javascript");



// let studentname = "Fenil";
// let course = "Information Technology";
// let semester = "3";
// console.log(studentname);
// console.log(course);
// console.log(semester);


// let college = "CHARUSAT";
// let year = "2026";
// let isStudent = True;
// console.log(college);
// console.log(year);
// console.log(isStudent);


function welcomepage(){
    console.log("Welcome To The StudentHub.")
}

welcomepage();

function student(name, mycourse){
    console.log("Hii, I Am " + name + " I Am In " + mycourse + " Departement.");
}

// student("Fenil Rathod","IT");



// let changeHeading = document.getElementById("heading");
// // changeHeading.onclick = ;



/* =========================================================
   STUDENTHUB JAVASCRIPT
   ========================================================= */

console.log("StudentHub Javascript Loaded Successfully.");
console.log("Welcome To The StudentHub.");
console.log("Practical-4 Javascript");


/* =========================================================
   BASIC JAVASCRIPT VARIABLES
   ========================================================= */

let studentname = "Fenil";
let course = "Information Technology";
let semester = "3";

console.log(studentname);
console.log(course);
console.log(semester);


let college = "CHARUSAT";
let year = "2026";
let isStudent = true;

console.log(college);
console.log(year);
console.log(isStudent);


/* =========================================================
   WELCOME FUNCTION
   ========================================================= */

function welcomepage() {

    console.log("Welcome To The StudentHub.");

}

welcomepage();


/* =========================================================
   STUDENT FUNCTION
   ========================================================= */

function student(name, mycourse) {

    console.log(
        "Hii, I Am " +
        name +
        " I Am In " +
        mycourse +
        " Department."
    );

}

student("Fenil Rathod", "IT");


/* =========================================================
   WAIT FOR HTML TO LOAD
   ========================================================= */

document.addEventListener("DOMContentLoaded", function () {


    /* =====================================================
       DARK MODE
       ===================================================== */

    const themeToggle =
        document.getElementById("theme-toggle");


    const root =
        document.documentElement;


    /*
       Get previously saved theme.

       If no theme has been saved,
       use light mode.
    */

    const savedTheme =
        localStorage.getItem("studenthub-theme");


    if (savedTheme === "dark") {

        root.setAttribute("data-theme", "dark");

    } else {

        root.setAttribute("data-theme", "light");

    }


    /* Update button icon */

    function updateThemeButton() {

        if (!themeToggle) {
            return;
        }


        const isDark =
            root.getAttribute("data-theme") === "dark";


        if (isDark) {

            themeToggle.textContent = "☀️";

            themeToggle.setAttribute(
                "aria-label",
                "Switch to light mode"
            );

            themeToggle.setAttribute(
                "title",
                "Switch to light mode"
            );

        } else {

            themeToggle.textContent = "🌙";

            themeToggle.setAttribute(
                "aria-label",
                "Switch to dark mode"
            );

            themeToggle.setAttribute(
                "title",
                "Switch to dark mode"
            );

        }

    }


    updateThemeButton();


    /* Theme toggle click */

    if (themeToggle) {

        themeToggle.addEventListener(
            "click",
            function () {

                const currentTheme =
                    root.getAttribute("data-theme");


                if (currentTheme === "dark") {

                    root.setAttribute(
                        "data-theme",
                        "light"
                    );

                    localStorage.setItem(
                        "studenthub-theme",
                        "light"
                    );

                } else {

                    root.setAttribute(
                        "data-theme",
                        "dark"
                    );

                    localStorage.setItem(
                        "studenthub-theme",
                        "dark"
                    );

                }


                updateThemeButton();

            }
        );

    }


    /* =====================================================
       HEADING CHANGE
       ===================================================== */

    const heading =
        document.getElementById("hero-heading");


    const headingChangeButton =
        document.getElementById(
            "heading-change-btn"
        );


    if (
        heading &&
        headingChangeButton
    ) {

        headingChangeButton.addEventListener(
            "click",
            function () {

                heading.innerHTML =
                    'Learn. <span>Connect. Grow.</span>';

            }
        );

    }


    /* =====================================================
       FAQ ACCORDION
       ===================================================== */

    const faqQuestions =
        document.querySelectorAll(
            ".faq-question"
        );


    faqQuestions.forEach(function (question) {

        question.addEventListener(
            "click",
            function () {

                const faqItem =
                    question.closest(".faq-item");


                const isCurrentlyOpen =
                    faqItem.classList.contains("open");


                /*
                   Close all FAQ items.
                   This means only one answer
                   stays open at a time.
                */

                document
                    .querySelectorAll(".faq-item")
                    .forEach(function (item) {

                        item.classList.remove("open");

                        const itemButton =
                            item.querySelector(
                                ".faq-question"
                            );

                        const itemIcon =
                            item.querySelector(
                                ".faq-icon"
                            );


                        if (itemButton) {

                            itemButton.setAttribute(
                                "aria-expanded",
                                "false"
                            );

                        }


                        if (itemIcon) {

                            itemIcon.textContent = "+";

                        }

                    });


                /*
                   If the clicked FAQ was closed,
                   open it.
                */

                if (!isCurrentlyOpen) {

                    faqItem.classList.add("open");

                    question.setAttribute(
                        "aria-expanded",
                        "true"
                    );


                    const icon =
                        question.querySelector(
                            ".faq-icon"
                        );


                    if (icon) {

                        icon.textContent = "−";

                    }

                }

            }
        );

    });

});

