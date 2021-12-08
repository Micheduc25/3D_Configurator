var iframe = document.getElementById("api-frame");
var DEFAULT_URLID = "348778fc87b54be6b83ccd311b63fa3d";
var DEFAULT_PREFIX = "woodzip1 ";
const availableTextures = {
  defaultsIndexes: {
    bardages: 9,
    enduits: 10,
  },

  bardages: {
    bois_blanc: {
      name: "Bois Blanc",
      url: "https://3d.woodzip.com/Textures/Bardage%20bois%20blanc.jpg",
      price: 1700,
    },
    bois_clair: {
      name: "Bois Clair",
      url: "https://3d.woodzip.com/Textures/Bardage%20bois%20clair.jpg",
      price: 2000,
    },

    bois_naturel: {
      name: "Bois Naturel",
      url: "https://3d.woodzip.com/Textures/Bardage%20bois%20naturel.jpg",
      price: 2500,
    },
  },

  enduits: {
    blanc: {
      name: "Blanc",
      url: "https://3d.woodzip.com/Textures/Enduit%20blanc.jpg",
      price: 1000,
    },
    gris: {
      name: "Gris",
      url: " https://3d.woodzip.com/Textures/Enduit%20gris.jpg",
      price: 1200,
    },

    marron: {
      name: "Beige",
      url: "https://3d.woodzip.com/Textures/Enduit%20marron.jpg",
      price: 1500,
    },

    gris_fonce:{
      name: "Gris foncé",
      url:"https://3d.woodzip.com/Textures/Enduit%20gris%20fonce.jpg"
    }
  },
};
var CONFIG = {
  urlid: DEFAULT_URLID,
  prefix: DEFAULT_PREFIX,
};

var Configurator = {
  api: null,
  config: null,
  options: [],
  materials: [],
  defaultMaterial: null,
  textures: [],
  currentBardages: "bois_naturel",
  currentEnduits: "blanc",
  nodes: [],
  texturesCost: 0,
  extensionsCost: 0,
  basePrice: 199900,
  showWithTVA: false,
  totalCostWithoutTVA:199900,
  totalCostWithTVA() {
    return this.totalCostWithoutTVA + this.totalCostWithoutTVA * 0.2;
  }
  
 ,

  /**
   * Initialize viewer
   */
  init: function (config, iframe) {
    this.config = config;
    var client = new Sketchfab(iframe);
    client.init(config.urlid, {
      ui_infos: 0,
      ui_controls: 1,
      ui_watermark: 0,
      graph_optimizer: 0,
      autostart: 1,
      preload: 1,
      success: function onSuccess(api) {
        toggleBarrage(true);
        api.start();
        api.addEventListener(
          "viewerready",
          function () {
            this.api = api;

            this.getTextureList(() => {
              UI.init(CONFIG);
              availableTextures.defaultsIndexes = {
                enduits: this.textures.findIndex(
                  (texture) => texture.name === "Enduit_blanc.jpg"
                ),
                bardages: this.textures.findIndex(
                  (texture) => texture.name === "Bardage_bois_naturel.jpg"
                ),
              };
              console.log(availableTextures.defaultsIndexes);
              UI.selectConfig("bardages", this.currentBardages, () => {
                UI.selectConfig("enduits", this.currentEnduits, () => {
                  this.initializeNodes(() => {
                    UI.toggleExtension("Garage", true, () => {
                      UI.toggleExtension("Terrasse", true, () => {
                        UI.generatePriceDetails();
                      });
                    });
                    toggleBarrage(false);
                  });
                });
              });
            });
          }.bind(this)
        );
      }.bind(this),
      error: function onError() {
        console.log("Viewer error");
      },
    });
  },

  /**
   * Initialize options from scene
   */
  initializeNodes(callback) {
    this.api.getNodeMap((err, nodes) => {
      if (err) {
        console.error(err);
        return;
      }
      // console.log(nodes);

      var node;
      var isOptionObject = false;
      var keys = Object.keys(nodes);
      for (var i = 0; i < keys.length; i++) {
        node = nodes[keys[i]];
        isOptionObject = node.name === "Garage" || node.name === "Terrasse";
        if (isOptionObject) {
          this.nodes.push({
            id: node.instanceID,
            name: node.name,
            price: node.name === "Garage" ? 17900 : 4500,
            selected: true,
          });
        }
      }
      // console.log(this.nodes);
      callback();
    });
  },

  /**
   * Select option to show
   */
  selectOption: function selectOption(index) {
    var options = this.options;
    for (var i = 0, l = options.length; i < l; i++) {
      if (i === index) {
        options[i].selected = true;
        this.api.show(options[i].id);
      } else {
        options[i].selected = false;
        this.api.hide(options[i].id);
      }
    }
  },

  // setMaterial: function (ob, fromIndex, toIndex) {
  //   const newMaterial = ob.materials[toIndex];
  //   ob.materials[fromIndex].channels.DiffuseColor.enable = false;

  //   ob.materials[fromIndex] = newMaterial;

  //   ob.api.setMaterial(ob.materials[fromIndex], function () {
  //     console.log("material updated!", ob.materials[fromIndex]);
  //   });
  // }.bind(this),

  getTextureList: function getTextureList(cb) {
    this.api.getTextureList((err, textures) => {
      if (!err) {
        console.log("textures===> ", textures);
        this.textures = textures;
        cb();
      } else {
        console.log("an error occured", err);
      }
    });
  },

  updateTexture: function updateTexture(textureURL, textureUid, cb) {
    this.api.updateTexture(textureURL, textureUid, (err, updateTextureUid) => {
      if (!err) {
        // console.log("texture updated!");
        cb();
      } else console.log("could not update texture");
    });
  },

  addTexture: function addTexture(url) {
    this.api.addTexture(url, (err, textureId) => {
      if (!err) {
        this.getTextures();
      } else {
        console.log("unable to add texture");
      }
    });
  },

  selectConfigOption(category = "bardages", name, cb) {
    if (category === "bardages") {
      const bardageIndex = availableTextures.defaultsIndexes.bardages;
      // console.log(this.textures);
      this.updateTexture(
        availableTextures.bardages[name]["url"],

        this.textures[bardageIndex].uid,
        () => {
          this.currentBardages = name;
          cb();
        }
      );
    } else if (category === "enduits") {
      const enduitIndex = availableTextures.defaultsIndexes.enduits;
      this.updateTexture(
        availableTextures.enduits[name]["url"],
        this.textures[enduitIndex].uid,
        () => {
          this.currentEnduits = name;
          cb();
        }
      );
    }
  },

  getNodeMap(cb) {
    this.api.getNodeMap((err, nodes) => {
      if (!err) {
        this.nodes = nodes;
        // console.log(nodes);
        cb();
      } else {
        console.log("could not load nodes");
      }
    });
  },

  hideNode(instanceId, cb) {
    this.api.hide(instanceId, (err) => {
      if (!err) {
        // console.log("we have hidden ", instanceId);
      } else {
        console.log("could not hide node please try again");
      }
    });
  },

  showNode(instanceId, cb) {
    this.api.show(instanceId, (err) => {
      if (!err) {
        // console.log("we have shown ", instanceId);
      } else {
        console.log("could not show node please try again");
      }
    });
  },

  toggleNode: function toggleNode(instanceId, show = true, cb) {
    if (show) {
      this.api.show(instanceId, (err) => {
        if (!err) {
          cb();
          // console.log("we have shown ", instanceId);
        } else {
          console.error("could not show node please try again");
        }
      });
    } else {
      this.api.hide(instanceId, (err) => {
        if (!err) {
          cb();
          // console.log("we have hidden ", instanceId);
        } else {
          console.error("could not hide node please try again");
        }
      });
    }
  },

  calculateExtensionsCost() {
    const garage = Configurator.nodes.find((node) => node.name === "Garage");
    const terrasse = Configurator.nodes.find(
      (node) => node.name === "Terrasse"
    );

    if (!garage.selected && !terrasse.selected) this.extensionsCost = 0;
    else if (!garage.selected && terrasse.selected)
      this.extensionsCost = terrasse.price;
    else if (garage.selected && !terrasse.selected)
      this.extensionsCost = garage.price;
    else this.extensionsCost = garage.price + terrasse.price;

    return this.extensionsCost;
  },

  async submitConfigData() {
    const submitForm = document.createElement("form");
    submitForm.style.visibility = "hidden";
    submitForm.setAttribute("method", "POST");
    submitForm.setAttribute("action", "devis.php");

    const formData = {
      bardages: availableTextures.bardages[this.currentBardages]["name"],
      enduits: availableTextures.enduits[this.currentEnduits]["name"],
      terrasse: this.nodes.find((node) => node.name === "Terrasse").selected,
      garage: this.nodes.find((node) => node.name == "Garage").selected,
    };

    for (key of Object.keys(formData)) {
      var input = document.createElement("input");
      input.name = key;
      input.value = formData[key];
      submitForm.appendChild(input);
    }

    document.body.appendChild(submitForm);
    toggleBarrage(true);
    submitForm.submit();
    toggleBarrage(false);
  },

  toggleTVA(type="exclude"){

    Configurator.showWithTVA = type!=="exclude";
  }
};

var UI = {
  config: null,
  sublist_el: null,
  config_list_el: null,
  cost_el: null,
  init: function init(config) {
    this.config = config;
    this.sublist_el = document.getElementById("sub-items-list");
    this.config_list_el = document.getElementById("hconfig-items-list");
    this.cost_el = document.getElementById("total-cost");

    document.querySelector(".tva-text").innerText =
    Configurator.showWithTVA ?"Incl, 20% T.V.A": "Excl, 20% T.V.A";
  },

  selectConfig(category = "bardages", name, cb) {
    // toggleBarrage(true);
    Configurator.selectConfigOption(category, name, () => {
      this.renderConfigListItems(
        category,
        category === "bardages" || category === "enduits"
      );
      // const currentBardagesCost =
      //   availableTextures.bardages[Configurator.currentBardages]["price"];
      // const currentEnduitsCost =
      //   availableTextures.enduits[Configurator.currentEnduits]["price"];

      // Configurator.texturesCost = currentBardagesCost + currentEnduitsCost;

      // this.cost_el.textContent =
      //   Configurator.texturesCost + Configurator.extensionsCost + "";

      // toggleBarrage(false);
      if (cb) cb();
    });
  },

  toggleExtension(name, value, cb) {
    // toggleBarrage(true);
    if (name === "Garage") {
      const garage = Configurator.nodes.find((node) => node.name === "Garage");
      if (garage) {
        Configurator.toggleNode(garage.id, value, () => {
          garage.selected = value;
          this.renderExtensionsConfigList();
          this.generatePriceDetails();


          Configurator.totalCostWithoutTVA =
            Configurator.basePrice + Configurator.calculateExtensionsCost();


          console.log("with tva",Configurator.totalCostWithTVA());

          this.cost_el.textContent = Configurator.showWithTVA
            ? Configurator.totalCostWithTVA()
            : Configurator.totalCostWithoutTVA + "";

          // document.querySelector(".tva-text").innerText =
          //   Configurator.showWithTVA ? "Excl, 20% T.V.A" : "Incl, 20% T.V.A";
          // toggleBarrage(false);
          if (cb) cb();
        });
      }
    } else if (name === "Terrasse") {
      const terrasse = Configurator.nodes.find(
        (node) => node.name === "Terrasse"
      );
      if (terrasse) {
        Configurator.toggleNode(terrasse.id, value, () => {
          terrasse.selected = value;
          this.renderExtensionsConfigList();
          this.generatePriceDetails();

          Configurator.totalCostWithoutTVA =
            Configurator.basePrice + Configurator.calculateExtensionsCost();

          this.cost_el.textContent = Configurator.showWithTVA
            ? Configurator.totalCostWithTVA()
            : Configurator.totalCostWithoutTVA + "";

          // document.querySelector(".tva-text").innerText =
          //   Configurator.showWithTVA ? "Excl, 20% T.V.A" : "Incl, 20% T.V.A";

          if (cb) cb();

          // toggleBarrage(false);
        });
      }
    }
  },

  renderConfigListItems(category = "bardages") {
    let html = "";
    // console.log(availableTextures[category]);
    let i = 0;
    for (let key of Object.keys(availableTextures[category])) {
      let checkedState;

      if (
        (category === "bardages" && Configurator.currentBardages === key) ||
        (category === "enduits" && Configurator.currentEnduits === key)
      )
        checkedState = 'checked="checked"';
      else checkedState = "";
      html += `<div 
                onclick="${
                  "UI.selectConfig('" + category + "','" + key + "')"
                }" 
                class="hconfig-item"
                >
                <input 
                type="radio" 
                name="${category}"  
                ${checkedState} 
                id="${key}" 
                 />
                <div class="item-int">
                    <img src="${
                      availableTextures[category][key]["url"]
                    }" alt="image" />
                    <div style="display:flex; align-items:center;" class="">
                    <div style="text-transform:capitalize;" class="item-name">${
                      availableTextures[category][key]["name"]
                    }</div>
                    <!--
                    <div class="item-price">€ ${
                      availableTextures[category][key]["price"]
                    }</div>
                    -->
                    </div>
                </div>
                </div>
            `.trim();


    }

    if (this.config_list_el) this.config_list_el.innerHTML = html;
  },

  renderExtensionsConfigList() {
    let html = "";

    for (let opt of Configurator.nodes) {
      let checkedState = "";
      if (opt.selected === true) checkedState = 'checked="checked"';
      html += `
      <div 
      onclick="${
        "UI.toggleExtension('" + opt.name + "'," + !opt.selected + ")"
      }" 
      class="hconfig-item"
      >
      <input 
      type="checkbox" 
      name="${opt.name}"  
      ${checkedState} 
      id="${opt.id}" 
       />
      <div class="item-int">
          <img src="${
            opt.name === "Garage"
              ? "./assets/garage.jpg"
              : "./assets/terrasse.jpg"
          }" alt="image" />
          <div class="">
          <div style="text-transform:capitalize;" class="item-name">${opt.name.toUpperCase()}</div>
          <div class="item-price">€ ${opt.price}</div>
          </div>
      </div>
      </div>
      `;
    }

    if (this.config_list_el) this.config_list_el.innerHTML = html;
  },

  generatePriceDetails() {
    const priceDetailsDiv = document.querySelector(".price-details");
    const priceMap = new Map();
    let html = "";
    priceMap.set("Maison de Base", Configurator.basePrice);
    for (let node of Configurator.nodes) {
      if (node.selected === true) priceMap.set(node.name, node.price);
    }

    priceMap.forEach((value, key) => {
      html +=
        `<div class="price-details__item"> <span>${key}</span> <span>€ ${value}</span> </div>`.trim();
    });

    priceDetailsDiv.innerHTML = html;
  },

  generateTechnicalDetails() {
    const technicalDetailsDiv = document.querySelector(".tech-xtics");
    const technicalMap = new Map();
  },

  //this method toggles the TVA buttons styles
  toggleTVA_UI(type="exclude",event){
    event.stopPropagation();
    Configurator.toggleTVA(type);
    
    

    const pressedButton = event.target;
    const tvaButtons = document.getElementsByClassName("tva-button");
    for(let i = 0; i < tvaButtons.length; i++)
      tvaButtons[i].classList.remove("selected");
    
    pressedButton.classList.add("selected");

    this.cost_el.textContent = Configurator.showWithTVA
    ? Configurator.totalCostWithTVA()
    : Configurator.totalCostWithoutTVA + "";

  document.querySelector(".tva-text").innerText =
    Configurator.showWithTVA ?"Incl, 20% T.V.A": "Excl, 20% T.V.A";

  }
};

Configurator.init(CONFIG, iframe);
