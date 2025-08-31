// --------------------- Start phone number --------------------------- //
const phoneInput = document.getElementById("phone-number");

// Prevent typing anything except digits and "+"
phoneInput.addEventListener("input", () => {
  phoneInput.value = phoneInput.value.replace(/[^\d+]/g, "");
});

const imageInput = document.getElementById("profile-image");
const imageError = document.getElementById("imageError");
const imagePreview = document.getElementById("imagePreview");
const previewImg = imagePreview.querySelector("img");

imageInput.addEventListener("change", function () {
  const file = this.files[0];

  if (!file) return;

  // Validate file type (must be image)
  if (!file.type.startsWith("image/")) {
    imageError.classList.remove("hidden");
    imagePreview.classList.add("hidden");
    previewImg.src = "";
    return;
  }

  imageError.classList.add("hidden");

  // Show preview
  const reader = new FileReader();
  reader.onload = function (e) {
    previewImg.src = e.target.result;
    imagePreview.classList.remove("hidden");
  };
  reader.readAsDataURL(file);
});

const coverInput = document.getElementById("cover-image");
const coverError = document.getElementById("coverError");
const coverPreview = document.getElementById("coverPreview");
const coverImg = coverPreview.querySelector("img");

coverInput.addEventListener("change", function () {
  const file = this.files[0];
  if (!file) return;

  if (!file.type.startsWith("image/")) {
    coverError.classList.remove("hidden");
    coverPreview.classList.add("hidden");
    coverImg.src = "";
    return;
  }

  coverError.classList.add("hidden");

  const reader = new FileReader();
  reader.onload = function (e) {
    coverImg.src = e.target.result;
    coverPreview.classList.remove("hidden");
  };
  reader.readAsDataURL(file);
});

// Remove profile image preview
const removeProfileBtn = document.getElementById("removeProfileImage");
removeProfileBtn.addEventListener("click", function () {
  previewImg.src = "";
  imagePreview.classList.add("hidden");
  imageInput.value = ""; // Clear the input
});

// Remove cover image preview
const removeCoverBtn = document.getElementById("removeCoverImage");
removeCoverBtn.addEventListener("click", function () {
  coverImg.src = "";
  coverPreview.classList.add("hidden");
  coverInput.value = ""; // Clear the input
});

const quillDescription = new Quill("#description-container", {
  theme: "snow",
  modules: {
    toolbar: "#description-toolbar",
  },
});

const allLanguages = [
  "Arabic",
  "English",
  "French",
  "Urdu",
  "Turkish",
  "Spanish",
  "Bengali",
  "Hindi",
  "Malay",
  "Russian",
  "Persian",
  "Chinese",
  "Swahili",
  "German",
  "Italian",
  "Portuguese",
  "Dutch",
  "Korean",
  "Japanese",
  "Greek",
  "Hebrew",
  "Thai",
  "Vietnamese",
  "Polish",
  "Indonesian",
  "Romanian",
  "Serbian",
  "Croatian",
  "Czech",
  "Slovak",
  "Danish",
  "Norwegian",
  "Swedish",
  "Finnish",
  "Zulu",
  "Somali",
  "Amharic",
  "Punjabi",
  "Tamil",
  "Telugu",
  "Marathi",
  "Gujarati",
  "Pashto",
  "Kurdish",
  "Azerbaijani",
  "Armenian",
  "Georgian",
  "Kazakh",
  "Uzbek",
  "Malayalam",
  "Sinhala",
  "Tigrinya",
  "Yoruba",
  "Hausa",
  "Igbo",
  "Javanese",
  "Tagalog",
  "Mongolian",
  "Nepali",
  "Burmese",
];

const languageSelect = document.getElementById("languages");
allLanguages.forEach((lang) => {
  const option = document.createElement("option");
  option.value = lang;
  option.text = lang;
  languageSelect.appendChild(option);
});

new TomSelect("#languages", {
  maxItems: null,
  create: true,
  persist: false,
  plugins: ["remove_button"],
  placeholder: "Type and select any language...",
});

document.addEventListener("DOMContentLoaded", () => {
  const addRowBtn = document.querySelector(".add-row-btn button");
  const contentContainer = document.querySelector(".content-container");

  let wrapperCount = 1;

  function initQuill(wrapper, index) {
    const toolbar = wrapper.querySelector(".date-toolbar");
    const container = wrapper.querySelector(".date-container");

    container.id = `date-container-${index}`;
    toolbar.id = `date-toolbar-${index}`;

    return new Quill(`#${container.id}`, {
      theme: "snow",
      modules: {
        toolbar: `#${toolbar.id}`,
      },
    });
  }

  function attachDatePicker(wrapper, index) {
    const input = wrapper.querySelector(".date-input");
    const label = wrapper.querySelector(".date-label");
    const output = wrapper.querySelector(".selected-date");

    input.id = `memorization-date-${index}`;
    input.name = `memorization-date-${index}`;
    label.setAttribute("for", input.id);
    output.id = `selected-date-${index}`;
    output.textContent = "";
    output.classList.add("hidden");

    label.addEventListener("click", () => {
      input.showPicker?.() || input.click();
    });

    input.addEventListener("change", () => {
      if (input.value) {
        const formatted = new Date(input.value).toLocaleDateString();
        output.textContent = `Selected Date: ${formatted}`;
        output.classList.remove("hidden");
      } else {
        output.classList.add("hidden");
      }
    });
  }

  // أول عنصر
  const firstWrapper = contentContainer.querySelector(".wrapper");
  attachDatePicker(firstWrapper, wrapperCount);
  initQuill(firstWrapper, wrapperCount);

  addRowBtn.addEventListener("click", () => {
    wrapperCount++;

    const originalWrapper = contentContainer.querySelector(".wrapper");
    const newWrapper = originalWrapper.cloneNode(true);

    // Clear previous content
    const newInput = newWrapper.querySelector(".date-input");
    const newLabel = newWrapper.querySelector(".date-label");
    const newP = newWrapper.querySelector(".selected-date");
    const newEditor = newWrapper.querySelector(".date-container");

    newInput.value = "";
    newP.textContent = "";
    newEditor.innerHTML = "";

    contentContainer.appendChild(newWrapper);

    attachDatePicker(newWrapper, wrapperCount);
    initQuill(newWrapper, wrapperCount);
  });
});

const mapInput = document.getElementById("map-link");
const mapError = document.getElementById("mapError");
const mapPreview = document.getElementById("mapPreview");
const mapFrame = document.getElementById("mapFrame");

// Regex to extract coordinates from Google Maps link
const coordsRegex = /@(-?\d+\.\d+),(-?\d+\.\d+)/;

mapInput.addEventListener("input", function () {
  const value = this.value.trim();
  const match = value.match(coordsRegex);

  if (match) {
    const lat = match[1];
    const lng = match[2];
    const embedUrl = `https://www.google.com/maps?q=${lat},${lng}&output=embed`;

    mapError.classList.add("hidden");
    mapFrame.src = embedUrl;
    mapPreview.classList.remove("hidden");
  } else {
    mapFrame.src = "";
    mapPreview.classList.add("hidden");
    if (value !== "") {
      mapError.classList.remove("hidden");
    } else {
      mapError.classList.add("hidden");
    }
  }
});

const switchQrInput = document.getElementById("switch-qr");
const switchQrError = document.getElementById("switchQrError");
const switchQrPreview = document.getElementById("switchQrPreview");
const switchQrImg = switchQrPreview.querySelector("img");
const removeSwitchQrBtn = document.getElementById("removeSwitchQrImage");

switchQrInput.addEventListener("change", function () {
  const file = this.files[0];
  if (!file) return;

  if (!file.type.startsWith("image/")) {
    switchQrError.classList.remove("hidden");
    switchQrPreview.classList.add("hidden");
    switchQrImg.src = "";
    return;
  }

  switchQrError.classList.add("hidden");

  const reader = new FileReader();
  reader.onload = function (e) {
    switchQrImg.src = e.target.result;
    switchQrPreview.classList.remove("hidden");
  };
  reader.readAsDataURL(file);
});

removeSwitchQrBtn.addEventListener("click", function () {
  switchQrImg.src = "";
  switchQrPreview.classList.add("hidden");
  switchQrInput.value = "";
});
