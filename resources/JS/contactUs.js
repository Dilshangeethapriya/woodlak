function showContent(contentId) {
  const tabs = document.querySelectorAll(".tab-button");
  const contents = document.querySelectorAll(".tab-content");

  tabs.forEach((tab) => tab.classList.remove("active"));
  contents.forEach((content) => content.classList.add("hidden"));

  document.getElementById(contentId).classList.remove("hidden");
  document
    .querySelector(`.tab-button[onclick="showContent('${contentId}')"]`)
    .classList.add("active");
}

document.addEventListener("DOMContentLoaded", () => showContent("inquiry"));
