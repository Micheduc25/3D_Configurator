// const initMap = () => {
  const barrage2 = document.querySelector(".barrage2");
  barrage2.classList.add("show");

  let wz_map;
  let geocoder;
  let marker2;

  if (mapboxgl) {

    mapboxgl.accessToken =
      "pk.eyJ1IjoibWljaGVkdWMyNSIsImEiOiJja3h4bGhwcGUxczQ0Mm5vemowOTRsNXdrIn0.CPAQumBkBT2qIEjbceHeSA";
    wz_map = new mapboxgl.Map({
      container: "map", // container ID
      // style: "mapbox://styles/mapbox/streets-v11", // style URL
      style: 'mapbox://styles/micheduc25/ckylvapm3788o14lea1yew25u', // style URL
      center: [2.349014, 48.864716], // starting position(Paris)
      zoom: 13, // starting zoom
      preserveDrawingBuffer: true,
    });

    wz_map.on("load", (e) => {
      
      barrage2.classList.remove("show");

      document.querySelector(".save-button").style.display = "inline-block";

      wz_map
      .setLayoutProperty("country-label", "text-field", [
        "get",
        `name_fr`,
      ]);

      const geojson = {
        type: "FeatureCollection",
        features: [
          {
            type: "Feature",
            geometry: {
              type: "Point",
              coordinates: [2.349014, 48.864716],
            },
            properties: {
              title: "Emplacement",
              description: "Emplacement de la maison",
            },
          },
        ],
      };

      // add markers to map
      for (const feature of geojson.features) {
        // create a HTML element for each feature
        const el = document.createElement("div");
        el.className = "marker";
        console.log("adding marker");
        // make a marker for each feature and add to the map
        marker2 = new mapboxgl.Marker({ element: el, draggable: true })
          .setLngLat(feature.geometry.coordinates)
          .addTo(wz_map);
      }


      wz_map.on("click", (e) => {
        // console.log("map clicked at ", e.lngLat);

        marker2.setLngLat([e.lngLat.lng, e.lngLat.lat]);
      });

      // Add the control to the wz_map.
      //geocoder to search for places
      geocoder = new MapboxGeocoder({
        accessToken: mapboxgl.accessToken,
        mapboxgl: mapboxgl,
      });
      wz_map.addControl(geocoder);
    });

    wz_map.on("error", (e) => {
      console.log(
        "Une erreure de connexion est survenue.",
        Object.keys(e).toString()
      );
    });
  } else {
    barrage2.classList.remove("show");

    const errorMessage = document.querySelector(".error-message");

    errorMessage.textContent =
      "Une erreur de chargement est survenu. Veuillez vérifier votre connexion internet.";
  }

  function screenshotMap() {
    if (html2canvas) {
      const allMap = document.querySelector("#map");

      // const mapCanvas = document.querySelector(".mapboxgl-canvas");
      wz_map.removeControl(geocoder);
      barrage2.classList.add("show");
      document.querySelector(".marker").style.width = "80px";
      document.querySelector(".marker").style.height = "80px";
      html2canvas(allMap)
        .then(function (canvas) {
          //navigate back to main page
          const imageInput = document.querySelector('#imageData');
          const locationInput = document.querySelector('#location');
          imageInput.value = canvas.toDataURL();
          const currentPosition = marker2.getLngLat() || {lng:2.349014, lat:48.864716};
          locationInput.value = `Coordonnées (latitude, longitude): ${currentPosition.lat}, ${currentPosition.lng}`;

          // var anchorTag = document.createElement("a");
          // document.body.appendChild(anchorTag);
          //   document.getElementById("previewImg").appendChild(canvas);
          // anchorTag.download = "map1.jpg";
          // anchorTag.href = canvas.toDataURL();
          // anchorTag.target = "_blank";
          // anchorTag.click();
        })
        .finally(() => {
          barrage2.classList.remove("show");
          document.querySelector(".marker").style.width = "50px";
          document.querySelector(".marker").style.height = "50px";
          wz_map.addControl(geocoder);
          toggleMap(false);

          toggleToast(true, "Emplacement enregistré avec succèss");
          setTimeout(()=>{
             toggleToast(false);
          },3000);

        });
    } else {
      console.error("html2canvas library not successfully imported");
    }
  }

  document.getElementById('save-button').addEventListener('click',screenshotMap);
// };

function toggleMap(value=true){
  const map = document.querySelector('.wz_map');
    map.classList.toggle('show',value);

    if(value===true)
    wz_map.resize()

}