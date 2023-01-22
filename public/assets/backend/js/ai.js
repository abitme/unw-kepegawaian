/**
 * Number.prototype.format(n, x, s, c)
 *https://stackoverflow.com/questions/149055/how-to-format-numbers-as-currency-string
 *
 * @param integer n: length of decimal
 * @param integer x: length of whole part
 * @param mixed   s: sections delimiter
 * @param mixed   c: decimal delimiter
 */
Number.prototype.format = function (n, x, s, c) {
    var re = "\\d(?=(\\d{" + (x || 3) + "})+" + (n > 0 ? "\\D" : "$") + ")",
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace(".", c) : num).replace(
        new RegExp(re, "g"),
        "$&" + (s || ",")
    );
};

// https://stackoverflow.com/questions/41756911/sum-column-values-in-table
function getnum(t) {
    if (isNumeric(t)) {
        return parseInt(t, 10);
    }
    return 0;

    function isNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
}

function replaceAll(string, search, replace) {
    return string.split(search).join(replace);
}

function previewImage() {
    const img = document.querySelector("#image");
    const label = document.querySelector(".custom-file-label");
    const imgPreview = document.querySelector(".img-preview");

    // change url browse
    label.textContent = img.files[0].name;

    // change img preview
    const fileImage = new FileReader();
    fileImage.readAsDataURL(img.files[0]);

    fileImage.onload = function (e) {
        imgPreview.src = e.target.result;
    };
}

function labelImageFile() {
    window.onchange = (e) => {
        // get .img-preview-proof
        const img =
            e.target.parentNode.previousElementSibling.firstElementChild
                .firstElementChild;

        // set label
        const fileLabel = e.target.files[0].name;
        e.target.nextElementSibling.textContent = fileLabel;

        // change img preview
        const fileImage = new FileReader();
        fileImage.readAsDataURL(e.target.files[0]);

        fileImage.onload = function (el) {
            img.src = el.target.result;
        };

        // remove gambarLama
        e.target.previousElementSibling.remove();
    };
}

function labelImageFileNoRemove() {
    window.onchange = (e) => {
        // get .img-preview-proof
        const img =
            e.target.parentNode.previousElementSibling.firstElementChild
                .firstElementChild;
        console.log(img);
        // set label
        const fileLabel = e.target.files[0].name;
        e.target.nextElementSibling.textContent = fileLabel;

        // change img preview
        const fileImage = new FileReader();
        fileImage.readAsDataURL(e.target.files[0]);

        fileImage.onload = function (el) {
            img.src = el.target.result;
        };
    };
}

function labelFile() {
    window.onchange = (e) => {
        // set label
        const fileLabel = e.target.files[0].name;
        e.target.nextElementSibling.textContent = fileLabel;

        // remove gambarLama
        e.target.previousElementSibling.remove();
    };
}

function labelFileNoRemove() {
    window.onchange = (e) => {
        // set label
        if (e.target.type == "file") {
            const fileLabel = e.target.files[0].name;
            e.target.nextElementSibling.textContent = fileLabel;
        }
    };
}

function createSlug() {
    let judul = $("#judul").val();
    $("#slug").val(string_to_slug(judul));
}

function string_to_slug(str) {
    str = str.replace(/^\s+|\s+$/g, ""); // trim
    str = str.toLowerCase();

    // remove accents, swap ñ for n, etc
    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
    var to = "aaaaeeeeiiiioooouuuunc------";
    for (var i = 0, l = from.length; i < l; i++) {
        str = str.replace(new RegExp(from.charAt(i), "g"), to.charAt(i));
    }

    str = str
        .replace(/[^a-z0-9 -]/g, "") // remove invalid chars
        .replace(/\s+/g, "-") // collapse whitespace and replace by -
        .replace(/-+/g, "-"); // collapse dashes

    return str;
}

// stackoverflow.com/questions/23593052/format-javascript-date-as-yyyy-mm-dd
function formatDate(date) {
    var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = "0" + month;
    if (day.length < 2) day = "0" + day;

    return [year, month, day].join("-");
}

// // tooltip bootstrap
// $(function () {
//   $('[data-toggle="tooltip"]').tooltip()
// })