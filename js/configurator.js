var iframe = document.getElementById("api-frame");
var DEFAULT_URLID = maisons[0].id; //"348778fc87b54be6b83ccd311b63fa3d";
var DEFAULT_PREFIX = "act_";

let currentMaison = maisons[0];
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

    gris_fonce: {
      name: "Gris foncé",
      url: "https://3d.woodzip.com/Textures/Enduit%20gris%20fonce.jpg",
    },
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
  showWithTVA: false,
  totalCostWithoutTVA: 199900,

  totalCostWithTVA() {
    return this.totalCostWithoutTVA + this.totalCostWithoutTVA * 0.2;
  },

  /**
   * Initialize viewer
   */
  init(config, iframe) {
    this.config = config;
    var client = new Sketchfab(iframe);
    this.nodes = [];
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
              UI.init(this.config);
              availableTextures.defaultsIndexes = {
                enduits: this.textures.findIndex(
                  (texture) => texture.name === "Enduit_blanc.jpg"
                ),
                bardages: this.textures.findIndex(
                  (texture) => texture.name === "Bardage_bois_naturel.jpg"
                ),
              };
              // console.log(availableTextures.defaultsIndexes);
              UI.selectConfig("bardages", this.currentBardages, () => {
                UI.selectConfig("enduits", this.currentEnduits, () => {
                  this.initializeNodes(() => {
                    let i = 0;
                    for (let node of this.nodes) {
                      this.toggleNode(node.instanceID, node.selected, () => {
                        const nodeIndex = this.nodes.findIndex(
                          (n) => n.instanceID === node.instanceID
                        );
                        if (nodeIndex === this.nodes.length - 1) {
                          UI.cost_el.textContent = this.calculateCost() + "";
                        }
                      });

                      i++;
                    }

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

      let node;
      let isOptionObject = false;
      let keys = Object.keys(nodes);

      let opt_ids = [];
      for (var i = 0; i < keys.length; i++) {
        node = nodes[keys[i]];
        isOptionObject =
          node.name &&
          node.name.startsWith(this.config.prefix) &&
          node.type === "Group";

        if (isOptionObject) {
          //we find the corresponding option from the database and set its display name
          let opt = maisonOptions.find(
            (op) =>
              op.nom.includes(node.name) &&
              this.config.urlid === op.maison_id &&
              !opt_ids.includes(op.id)
          );
          if (opt) {
            if (opt.depends_on !== null && typeof opt.depends_on === "string") {
              opt.depends_on = opt.depends_on
                .split(" ")
                .map((id) => parseInt(id));
            }
            opt.id = parseInt(opt.id);
            opt.visible_on_init = opt.visible_on_init === "1" ? true : false;
            opt.prix = parseFloat(opt.prix);

            if (
              opt.hide_when_visible !== null &&
              typeof opt.hide_when_visible === "string"
            ) {
              opt.hide_when_visible = opt.hide_when_visible
                .split(" ")
                .map((id) => parseInt(id));
            }
            if (
              opt.mutually_excludes !== null &&
              typeof opt.mutually_excludes === "string"
            ) {
              opt.mutually_excludes = opt.mutually_excludes
                .split(" ")
                .map((id) => parseInt(id));
            }
            opt.instanceID = node.instanceID;
            opt.selected = opt.visible_on_init;

            this.nodes.push(opt);
            opt_ids.push(opt.id);
          }
        }
      }

      //We add other options which are not found in the list of nodes but are in the database.
      for (opt of maisonOptions) {
        if (!opt_ids.includes(opt.id) && opt.maison_id === this.config.urlid) {
          if (opt.depends_on !== null && typeof opt.depends_on === "string") {
            opt.depends_on = opt.depends_on
              .split(" ")
              .map((id) => parseInt(id));
          }
          opt.id = parseInt(opt.id);
          opt.visible_on_init = opt.visible_on_init === "1" ? true : false;
          opt.prix = parseFloat(opt.prix);

          if (
            opt.hide_when_visible !== null &&
            typeof opt.hide_when_visible === "string"
          ) {
            opt.hide_when_visible = opt.hide_when_visible
              .split(" ")
              .map((id) => parseInt(id));
          }

          if (
            opt.mutually_excludes !== null &&
            typeof opt.mutually_excludes === "string"
          ) {
            opt.mutually_excludes = opt.mutually_excludes
              .split(" ")
              .map((id) => parseInt(id));
          }
          opt.instanceID = null;
          opt.selected = opt.visible_on_init;

          this.nodes.push(opt);
        }
      }

      // console.log(this.nodes);

      callback();
    });
  },

  getTextureList: function getTextureList(cb) {
    this.api.getTextureList((err, textures) => {
      if (!err) {
        // console.log("textures===> ", textures);
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
        if (cb) cb();
        // console.log("we have hidden ", instanceId);
      } else {
        console.log("could not hide node please try again ==>", instanceId);
      }
    });
  },

  showNode(instanceId, cb) {
    this.api.show(instanceId, (err) => {
      if (!err) {
        if (cb) cb();

        // console.log("we have shown ", instanceId);
      } else {
        console.log("could not show node please try again");
      }
    });
  },

  toggleNode(instanceId, show = true, cb) {
    if (!instanceId) return;
    if (show) {
      this.api.show(instanceId, (err) => {
        if (!err) {
          if (cb) cb();
          // console.log("we have shown ", instanceId);
        } else {
          console.error("could not show node please try again");
        }
      });
    } else {
      this.api.hide(instanceId, (err) => {
        if (!err) {
          if (cb) cb();

          // console.log("we have hidden ", instanceId);
        } else {
          console.error("could not hide node please try again ", instanceId);
        }
      });
    }
  },

  selectMaison(maisonId, cb) {
    if (currentMaison.id !== maisonId) {
      currentMaison = maisons.find((m) => m.id === maisonId);

      if (currentMaison) {
        this.init({ urlid: maisonId, prefix: DEFAULT_PREFIX }, iframe);
      }
    }
  },

  calculateCost() {
    let extensionsCost = 0.0;
    let totalCost = 0.0;
    const selectedOptions = this.nodes.filter((node) => node.selected);
    // console.error(selectedOptions);
    for (let opt of selectedOptions) {
      extensionsCost += opt.prix;
    }

    totalCost = extensionsCost + parseFloat(currentMaison.base_price);
    return totalCost;
  },

  async submitConfigData() {
    const submitForm = document.createElement("form");
    submitForm.style.visibility = "hidden";
    submitForm.setAttribute("method", "POST");
    submitForm.setAttribute("action", "devis.php");

    const formData = {
      bardages: availableTextures.bardages[this.currentBardages]["name"],
      enduits: availableTextures.enduits[this.currentEnduits]["name"],
    };

    for (let node of this.nodes) {
      if (node.nom.startsWith("whole"))
        formData[node.display_name.replace(" ", "_")] = node.selected
          ? "Oui"
          : "Non";
    }

    formData.totalCostWithoutTVA = this.calculateCost();
    formData.totalCostWithTVA =
      this.calculateCost() + this.calculateCost() * 0.2;

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

  toggleTVA(type = "exclude") {
    Configurator.showWithTVA = type !== "exclude";
  },
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

    document.querySelector(".tva-text").innerText = Configurator.showWithTVA
      ? "Incl, 20% T.V.A"
      : "Excl, 20% T.V.A";
  },

  selectConfig(category = "bardages", name, cb) {
    Configurator.selectConfigOption(category, name, () => {
      this.renderConfigListItems(
        category,
        category === "bardages" || category === "enduits"
      );

      if (cb) cb();
    });
  },

  toggleExtension(name, value, isFinalRender = true, cb) {
    // toggleBarrage(true);
    //we deep clone the current nodes in the configurator and work with it and we use the current nodes list as reference
    const currentNodesState = JSON.parse(JSON.stringify(Configurator.nodes));

    const getNode = (nom) => currentNodesState.find((n) => n.nom === nom);
    const getNodeMasters = (node) =>
      node.depends_on
        ? currentNodesState.filter((n) => node.depends_on.includes(n.id))
        : null;
    const getNodeSlaves = (node) =>
      currentNodesState.filter(
        (n) => n.depends_on && n.depends_on.includes(node.id)
      );
    const getHiddenNodes = (node) =>
      currentNodesState.filter(
        (n) => node.hide_when_visible && node.hide_when_visible.includes(n.id)
      );

    const node = getNode(name);

    //if the main node is to be toggled off we automatically toggle off the dependent nodes too.
    if (value === false) {
      //we get nodes that depend on the node which has to be toggled
      const dependingNodes = getNodeSlaves(node);

      //we toggle off the node
      node.selected = false;

      //we attempt to show back the nodes which were hidden when the node was visible

      if (node.hide_when_visible) {
        const hiddenNodes = getHiddenNodes(node);
        for (let opt of hiddenNodes) {
          // console.log("toggle 1");
          const opt_MasterNodes = getMasterNodes(opt);
          let showOpt = true;
          for (let n of opt_MasterNodes) {
            if (!n.selected) {
              showOpt = false;
              break;
            }
          }
          if (showOpt) {
            opt.selected = true;
          }
        }
      }

      for (let opt of dependingNodes) {
        opt.selected = false;

        //we attempt to show back nodes which were hidden when the dependent nodes were visible
        if (opt.hide_when_visible) {
          const opt_hiddenNodes = getHiddenNodes(opt);
          for (let n of opt_hiddenNodes) {
            // console.log("toggle");
            const opt_MasterNodes = getNodeMasters(n);
            let showOpt = true;
            for (let m of opt_MasterNodes) {
              if (!m.selected) {
                showOpt = false;
                break;
              }
            }
            if (showOpt) {
              n.selected = true;
            }
          }
        }
      }
    }

    // block of toggling the extension on
    else {
      //make sure all extensions it depends on are turned on if not we exit the method.
      if (node.depends_on) {
        const masterNodes = getMasterNodes(node);
        let canShowNode = true;
        for (let masterNode of masterNodes) {
          if (!masterNode.selected) {
            canShowNode = false;
            break;
          }
        }

        if (!canShowNode) {
          console.log("master node not present", node.nom);
          return;
        }
      }

      //we toggle the node on
      node.selected = true;

      //We toggle on the dependent nodes and making sure that the other master nodes on which they depend are equally on(selected)
      const dependingNodes = getNodeSlaves(node).sort((n1, n2) => {
        if (
          (n1.depends_on &&
            n2.depends_on &&
            n1.depends_on.length < n2.depends_on.length) ||
          (!n1.depends_on && n2.depends_on)
        ) {
          return -1;
        } else if (
          (n1.depends_on &&
            n2.depends_on &&
            n1.depends_on.length === n2.depends_on.length) ||
          (!n1.depends_on && !n2.depends_on)
        ) {
          return 0;
        } else return 1;
      });
      for (let opt of dependingNodes) {
        let allowShow = true;
        if (opt.depends_on) {
          const otherMasternodes = getNodeMasters(opt).filter(
            (n) => n.id !== node.id
          );

          for (let obj of otherMasternodes) {
            if (!obj.selected) {
              allowShow = false;
              break;
            }
          }
        }

        if (allowShow) {
          opt.selected = true;

          //hide any nodes which are not to be visible when the current dependent node is visible(selected)
          if (opt.hide_when_visible) {
            const subnodesToHide = getHiddenNodes(opt);

            subnodesToHide.forEach((n) => {
              n.selected = false;
            });
          }
        }
      }

      //we hide any node which is not to be visible when the node is toggled on

      if (node.hide_when_visible) {
        const nodesToHide = getHiddenNodes(node);

        nodesToHide.forEach((n) => {
          n.selected = false;
        });
      }
    }

    //we render the nodes based on their selected values
    if (isFinalRender) {
      console.log(currentNodesState, Configurator.nodes);
      let i = 0;
      for (let obj of currentNodesState) {
        if (obj.selected !== Configurator.nodes[i].selected) {
          Configurator.nodes[i].selected = obj.selected;

          if (obj.instanceID) {
            Configurator.toggleNode(obj.instanceID, obj.selected, () => {
              console.log(obj.selected ? "showing " : "hidding", obj.nom);
            });
          }
        }

        i++;
      }
    }

    this.renderExtensionsConfigList();
    this.cost_el.textContent = `${Configurator.calculateCost()}`;
  },

  selectMaisonUI(maisonId) {
    this.closeAllMenus();
    Configurator.selectMaison(maisonId);
  },

  renderConfigListItems(category = "bardages") {
    let html = "";
    // console.log(availableTextures[category]);
    // let i = 0;
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

    const nodesToToggle = Configurator.nodes.filter((node) =>
      node.nom.startsWith("whole")
    );

    for (let opt of nodesToToggle) {
      let checkedState = "";
      if (opt.selected === true) checkedState = 'checked="checked"';
      html += `
      <div 
      onclick="${
        "UI.toggleExtension('" + opt.nom + "'," + !opt.selected + ")"
      }" 
      class="hconfig-item"
      >
      <input 
      type="checkbox" 
      name="${opt.nom}"  
      ${checkedState} 
      id="node${opt.id}" 
       />
      <div class="item-int">
          <img src="${opt.image_url || "/assets/config.png"}" alt="image" />
          <div class="">
          <div style="text-transform:capitalize;" class="item-name">${
            opt.display_name
          }</div>
          <div class="item-price">€ ${opt.prix}</div>
          </div>
      </div>
      </div>
      `;
    }

    if (this.config_list_el) this.config_list_el.innerHTML = html;
  },

  renderHousesListItems() {
    let html = "";

    for (let maison of maisons) {
      if (currentMaison.id === maison.id) checkedState = 'checked="checked"';
      else checkedState = "";

      html += `<div 
                onclick="${"UI.selectMaisonUI(" + "'" + maison.id + "'" + ")"}" 
                class="hconfig-item"
                >
                <input 
                type="radio" 
                name="maison"  
                ${checkedState} 
                id="${maison.id}" 
                 />
                <div class="item-int">
                    <img src="${
                      maison.image_url || "./assets/services.png"
                    }" alt="icone maison" />
                    <div style="display:flex; align-items:center;" class="">
                    <div style="text-transform:capitalize;" class="item-name">${
                      maison.nom
                    }</div>
                    <!--
                    <div class="item-price">€ ${maison.base_price}</div>
                    -->
                    </div>
                </div>
                </div>
            `.trim();

      if (this.config_list_el) this.config_list_el.innerHTML = html;
    }
  },

  closeAllMenus() {
    const configMenu = document.getElementById("config-menu");
    configMenu.classList.remove("show");
  },

  generatePriceDetails() {
    const priceDetailsDiv = document.querySelector(".price-details");
    const priceMap = new Map();
    let html = "";
    priceMap.set("Maison de Base", currentMaison.base_price);
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
  toggleTVA_UI(type = "exclude", event) {
    event.stopPropagation();
    Configurator.toggleTVA(type);

    const pressedButton = event.target;
    const tvaButtons = document.getElementsByClassName("tva-button");
    for (let i = 0; i < tvaButtons.length; i++)
      tvaButtons[i].classList.remove("selected");

    pressedButton.classList.add("selected");

    this.cost_el.textContent = Configurator.showWithTVA
      ? Configurator.totalCostWithTVA()
      : Configurator.totalCostWithoutTVA + "";

    document.querySelector(".tva-text").innerText = Configurator.showWithTVA
      ? "Incl, 20% T.V.A"
      : "Excl, 20% T.V.A";
  },
};

Configurator.init(CONFIG, iframe);
