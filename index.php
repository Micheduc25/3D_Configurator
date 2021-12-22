<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      rel="apple-touch-icon"
      sizes="180x180"
      href="assets/apple-touch-icon.png"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="32x32"
      href="assets/favicon-32x32.png"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="16x16"
      href="assets/favicon-16x16.png"
    />
    <link rel="manifest" href="assets/site.webmanifest" />
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="css/utils.css" />

    <title>WOODZIP</title>

    <!-- Insert this script -->
    <script
      type="text/javascript"
      src="https://static.sketchfab.com/api/sketchfab-viewer-1.10.4.js"
    ></script>
  </head>

  <body>

    <?php 

      function queryTable($conn, $table, $fields, $where){

        if(isset($where)){
          $sql = "SELECT * FROM $table WHERE $where";
        }
        else{
        $sql = "SELECT * FROM $table";
      }

        $results = $conn->query($sql);

        if($results->num_rows > 0){
            
          $finalResults = array();
          while($row = $results->fetch_assoc()) {
            $tempArray = array();
            foreach($row as $key => $val){
              $tempArray[$key] = $val; 
            }
            array_push($finalResults,$tempArray); 
          }
          return $finalResults;
        } else{
          return false;
        }
      }

      //remoce this function later

    
    //Here we load the houses available in the database

    // $dbPassword = getenv("WOODZIP_DB_PASSWORD");
		// $dbName = getenv("WOODZIP_DB");
		// $dbUsername = getenv("WOODZIP_DB_USERNAME");
    $conn = mysqli_connect("localhost", "root", "", "3d_woodzip_com");
    // $conn = mysqli_connect("localhost", "3d_woodzip_com", "gMEb7sR8P1ZRoBJW", "3d_woodzip_com");

		
		// Check connection
		if($conn === false){
			die("ERROR: Could not connect... "
				. mysqli_connect_error());
		}

    $maisons = queryTable($conn,"maisons","",null);
    $options = queryTable($conn,"options","",null);

    
		
		// Close connection
		mysqli_close($conn);
    
    ?>

    <section class="page-3d">
      <aside class="sidebar">
        <div class="sidebar__upper">
          <div class="content-wrapper">
            <div class="logo">
              <img src="./assets/logo.jpg" alt="logo" />

              <a href="https://www.woodzip.com">&larr; Retour au site</a>

              <span onclick="toggleMainMenu()" class="toggle-menu-but"
                >&#10095;</span
              >
            </div>
            <div class="main-menu">
              <div
                onclick="toggleConfigMenu('maisons',false,event)"
                class="mainmenu-item maisons"
              >
                <div class="item-title maisons">
                  <strong>MODELE DE MAISON</strong>
                </div>
                <div class="item-desc maisons">CHOISIR LA MAISON A CONFIGURER</div>
              </div>
              <div
                onclick="toggleConfigMenu('enduits',true,event)"
                class="mainmenu-item"
              >
                <div class="item-title">
                  <strong> REVETEMENT PRINCIPAL</strong>
                </div>
                <div class="item-desc">CHANGER LE REVETEMENT PRINCIPAL</div>
              </div>

              <div
                onclick="toggleConfigMenu('bardages',true,event)"
                class="mainmenu-item"
              >
                <div class="item-title">
                  <strong> REVETEMENT SECONDAIRE </strong>
                </div>
                <div class="item-desc">CHANGER LE REVETEMENT SECONDAIRE</div>
              </div>

              <div
                onclick="toggleConfigMenu('options',false,event)"
                class="mainmenu-item"
              >
                <div class="item-title">
                  <strong> OPTIONS</strong>
                </div>
                <div class="item-desc">AJOUTER OU ENLEVER DES OPTIONS</div>
              </div>
            </div>
          </div>
        </div>

        <div class="sidebar__lower">
          <div class="config-item">
            <a 
            href="https://docs.google.com/document/d/1Kt8zVKYRNJh1hPW9PC-TScNGgysIHul9q4gFG3_ByY4/edit?usp=sharing"
            style="padding: 5px 0 10px">
              <span></span>
              <span>Descriptif technique</span>
            </a>

            <div class="tech-xtics"></div>
          </div>

          <div class="config-details">
            <div class="detail-upper">
              <a 
              href="https://docs.google.com/document/d/1j-zQ5xa9y9XuVEQ7EjVrdSlM_edPeyvLPT6hc3p-_fs/edit?usp=sharing" 
              style="padding: 5px 0 10px">
                <span></span>
                <span>Plans d'intérieur</span>
              </a>
              <div class="price-details"></div>
            </div>
            <div class="detail-lower">
              <div class="cost-title">Total prix</div>
              <div class="total-cost">
                <div>&euro; <span id="total-cost">0</span>,<span>00</span></div>
                <div class="tva-text"></div>
              </div>
            </div>
            <div class="toggle-tva">
              <button
                class="tva-button"
                onclick="UI.toggleTVA_UI('include',event)"
              >
                Incl. T.V.A
              </button>
              <button
                class="tva-button selected"
                onclick="UI.toggleTVA_UI('exclude',event)"
              >
                Excl. T.V.A
              </button>
            </div>
          </div>

          <div class="config-devis">
            <button onclick="toggleSharePopup()">
              <img src="./assets/share.png" alt="share icon" />
            </button>

            <button class="devis-but" onclick="Configurator.submitConfigData()">
              DEMANDER UN DEVIS
            </button>
          </div>
        </div>

        <!---- The the sub-menu which appears when we click on an item which has a sub menu  -->
        <!-- <div id="sub-menu" class="drawer-sub-menu">
          <div onclick="toggleSubMenu()" class="return-but">&#10094; Retour</div>

          <div class="item-title">EXTENSIONS</div>

          <div id="sub-items-list" class="sub-items-list">
          </div>
        </div> -->

        <!---- The config menu which appears when we click on a sub-menu item  -->
        <div id="config-menu" class="hconfig-menu">
          <div
            onclick="toggleConfigMenu()"
            style="margin-bottom: 10px"
            class="return-but"
          >
            &#10094; Retour
          </div>

          <div class="item-title config-menu-title"></div>

          <div id="hconfig-items-list" class="hconfig-items-list"></div>
        </div>
      </aside>
      <main class="main-content">
        <nav class="top-bar">
          <a href="https://www.woodzip.com">&larr; Retour au site</a>

          <a href="#"> &#9776; </a>
        </nav>

        <div class="content-3d">
          <!-- Loader div -->
          <div class="barrage">
            <div class="lds-dual-ring"></div>
          </div>
          <!-- Insert an empty iframe with attributes -->
          <iframe
            src=""
            id="api-frame"
            allow="autoplay; fullscreen; xr-spatial-tracking"
            xr-spatial-tracking
            execution-while-out-of-viewport
            execution-while-not-rendered
            web-share
            allowfullscreen
            mozallowfullscreen="true"
            webkitallowfullscreen="true"
          ></iframe>

          <form class="options"></form>
        </div>
      </main>

      <div class="placeholder-div"></div>

      <div class="partager-popup">
        <div class="popup-wrapper">
          <div class="popup-title">
            <span> Partager la configuration</span>
            <span onclick="toggleSharePopup()">&#10005;</span>
          </div>
          <div class="popup-social">
            <a
              target="_blank"
              href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2F3d.woodzip.com%2F"
              class="share-iconbut"
            >
              <span
                id="configuration-share-link-facebook"
                class="configurator-share-popup__button"
              >
                <img src="assets/facebook.png" alt="facebook logo" />
              </span>
            </a>

            <a
              target="_blank"
              href="http://twitter.com/intent/tweet?text=Configuration%20de%20la%maison%20Woodzip"
              class="share-iconbut"
            >
              <span
                id="configuration-share-link-twitter"
                class="configurator-share-popup__button"
              >
                <img src="assets/twitter.png" alt="twitter logo" />
              </span>
            </a>

            <a
              target="_blank"
              href="https://wa.me/?text=https://3d.woodzip.com"
              class="share-iconbut"
            >
              <span
                id="configuration-share-link-whatsapp"
                class="configurator-share-popup__button"
              >
                <img src="assets/whatsapp.png" alt="whatsapp logo" />
              </span>
            </a>

            <a
              href="mailto:?subject=Configuration%20de%20la%20maison%20Woodzip&amp;body=https://3d.woodzip.com"
              class="share-iconbut"
            >
              <span
                id="configuration-share-link-mail"
                class="configurator-share-popup__button"
              >
                <img src="assets/email.png" alt="email icon" />
              </span>
            </a>
          </div>
          <div class="popup-link">
            <label name="link">Lien</label>
            <input
              id="link"
              type="text"
              readonly="readonly"
              value="https://3d.woodzip.com"
              name="link"
            />
          </div>
          <div class="popup-copy">
            <button onclick="copyLink(event)">Copier le lien</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Initialize the viewer -->
    <?php
      
      if(isset($maisons) && isset($options)){
        
        $maisonEncoded = json_encode($maisons);
        $optionsEncoded = json_encode($options);

        echo "<script> const maisons = $maisonEncoded;  const maisonOptions = $optionsEncoded;</script>";
        echo '<script  src="./js/configurator.js"></script>';

    }
    ?>

    <script src="./js/main.js"></script>




  </body>
</html>
