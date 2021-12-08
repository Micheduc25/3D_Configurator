function toggleSubMenu(category, isTexture, e) {
  const subMenu = document.getElementById("sub-menu");
  subMenu.classList.toggle("show");
}

function toggleConfigMenu(category = "bardages", isTexture, e) {
  const configMenu = document.getElementById("config-menu");
  if (!configMenu.classList.contains("show")) {
    if (category !== "options" && isTexture === true) {
      UI.renderConfigListItems(category, isTexture);
      document.querySelector(".config-menu-title").textContent =
        category !== "bardages"
          ? "REVETEMENT PRINCIPAL"
          : "REVETEMENT SECONDAIRE";
    } else {
      UI.renderExtensionsConfigList();
      document.querySelector(".config-menu-title").textContent = "OPTIONS";
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


function toggleSharePopup(){
    document.querySelector(".partager-popup").classList.toggle("show");
}

function toggleMainMenu(){
  document.querySelector(".main-menu").classList.toggle("show");
}

function copyLink(e) {
    const copyText = document.getElementById("link");
  
    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */
  
     /* Copy the text inside the text field */
    navigator.clipboard.writeText(copyText.value);
  
    e.target.innerText="Lien copié!";
  }
