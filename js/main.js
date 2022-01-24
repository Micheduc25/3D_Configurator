function toggleSubMenu(category, isTexture, e) {
  const subMenu = document.getElementById("sub-menu");
  subMenu.classList.toggle("show");
}

/**
 * [toggleConfigMenu] function makes the side menu to appear or disappear
 * @param {String} category The category of the menu
 * @param {Boolean} isTexture Whether the menu to be oppened is a texture menu or not
 * @param {Event} e The event triggered by the click
 */

function toggleConfigMenu(category = "maisons", isTexture, e) {

  const configMenu = document.getElementById("config-menu");

  if (!configMenu.classList.contains("show")) {
    if (category !== "options" && isTexture === true) {
      //here we handle textures menu toggling
      UI.renderConfigListItems(category, isTexture);
      document.querySelector(".config-menu-title").textContent =
        category !== "bardages"
          ? "REVETEMENT PRINCIPAL"
          : "REVETEMENT SECONDAIRE";
    } else if (category === "options" && isTexture === false) {
      //here we handle extensions menu toggling
      UI.renderExtensionsConfigList();
      document.querySelector(".config-menu-title").textContent = "OPTIONS";
    } else if (category==="maisons") {
      //we handle the toggling of the houses menu here
      document.querySelector(".config-menu-title").textContent =
        "MODELE DE MAISON";
      UI.renderHousesListItems();
    }
  }
  configMenu.classList.toggle("show");
}

function toggleBarrage(show) {
  const loaderContainer = document.querySelector(".barrage");
  loaderContainer.classList.toggle("show", show);
}

function togglePriceDetails() {
  const detailsDiv = document.querySelector(".price-details");
  const techDiv = document.querySelector(".tech-xtics");

  if (
    techDiv.classList.contains("show") &&
    !detailsDiv.classList.contains("show")
  )
    techDiv.classList.remove("show");

  detailsDiv.classList.toggle("show");
  document.querySelector(".toggle-tva").classList.toggle("show");
}
function toggleTechDetails() {
  const detailsDiv = document.querySelector(".price-details");
  const techDiv = document.querySelector(".tech-xtics");

  if (
    detailsDiv.classList.contains("show") &&
    !techDiv.classList.contains("show")
  )
    detailsDiv.classList.remove("show");
  document.querySelector(".toggle-tva").classList.remove("show");

  techDiv.classList.toggle("show");
}

function toggleSharePopup() {
  document.querySelector(".partager-popup").classList.toggle("show");
}

function toggleMainMenu() {
  document.querySelector(".main-menu").classList.toggle("show");
}

function copyLink(e) {
  const copyText = document.getElementById("link");

  /* Select the text field */
  copyText.select();
  copyText.setSelectionRange(0, 99999); /* For mobile devices */

  /* Copy the text inside the text field */
  navigator.clipboard.writeText(copyText.value);

  e.target.innerText = "Lien copié!";
}
