<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

const props = defineProps({
  departLat: { type: [Number, String], default: null },
  departLng: { type: [Number, String], default: null },
  departLabel: { type: String, default: "" },
  arriveeLat: { type: [Number, String], default: null },
  arriveeLng: { type: [Number, String], default: null },
  arriveeLabel: { type: String, default: "" },
});

const emit = defineEmits(["update:depart", "update:arrivee", "update:distance"]);

// Centre par défaut : Antananarivo
const DEFAULT_CENTER = [-18.8792, 47.5079];
const DEFAULT_ZOOM = 13;

const mapContainer = ref(null);
let map = null;
let departMarker = null;
let arriveeMarker = null;
let routeLayer = null;

const activeTarget = ref("depart"); // 'depart' | 'arrivee' : ce que place un clic sur la carte
const departSearch = ref(props.departLabel || "");
const arriveeSearch = ref(props.arriveeLabel || "");
const departResults = ref([]);
const arriveeResults = ref([]);
const isLocating = ref(false);
const isRouting = ref(false);
const routeError = ref("");

let departSearchTimeout = null;
let arriveeSearchTimeout = null;

// Drapeaux : évitent que le remplissage automatique du champ (par code)
// déclenche l'autocomplétion et rouvre la liste de suggestions.
let skipDepartSearch = false;
let skipArriveeSearch = false;

const departIcon = L.icon({
  iconUrl:
    "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png",
  shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41],
});

const arriveeIcon = L.icon({
  iconUrl:
    "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png",
  shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41],
});

// Icône "position en direct" façon InDrive/Uber : point bleu qui pulse
const liveIcon = L.divIcon({
  className: "",
  html:
    '<div class="live-position-marker"><div class="live-position-marker__pulse"></div><div class="live-position-marker__dot"></div></div>',
  iconSize: [22, 22],
  iconAnchor: [11, 11],
});

const isLiveSharing = ref(false);
let liveWatchId = null;
let lastRouteComputeAt = 0;
let lastRouteComputePos = null;

// Partage de position à distance (2e appareil) : création du lien + polling
const isRemoteSharing = ref(false);
const isCreatingShareLink = ref(false);
const shareUrl = ref("");
const shareToken = ref("");
const remoteSharingStatus = ref("waiting"); // 'waiting' | 'active' | 'expired'
let remotePollingId = null;
let hasCenteredOnRemote = false;

// ---------------------------------------------------------------------------
// Noms de lieux
// ---------------------------------------------------------------------------

// Construit un nom court (lieu + quartier/ville) à partir d'un résultat Nominatim.
// Nécessite addressdetails=1 dans la requête. Repli : 2 premiers segments de display_name.
const shortName = (item) => {
  const a = item?.address ?? {};
  const place = item?.name || a.amenity || a.shop || a.building || a.tourism || a.road;
  const area =
    a.suburb ||
    a.neighbourhood ||
    a.quarter ||
    a.village ||
    a.town ||
    a.city ||
    a.municipality;
  const parts = [...new Set([place, area].filter(Boolean))];
  if (parts.length) return parts.join(", ");
  return (
    item?.display_name
      ?.split(",")
      .slice(0, 2)
      .join(",")
      .trim() || ""
  );
};

const reverseGeocode = async (lat, lng) => {
  const fallback = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
  try {
    const res = await fetch(
      `https://nominatim.openstreetmap.org/reverse?format=json&addressdetails=1&accept-language=fr&lat=${lat}&lon=${lng}`,
      { headers: { Accept: "application/json" } }
    );
    const data = await res.json();
    return shortName(data) || fallback;
  } catch (e) {
    return fallback;
  }
};

// Remplissage programmatique des champs texte, sans déclencher l'autocomplétion
const setDepartText = (text) => {
  if (departSearch.value !== text) {
    skipDepartSearch = true;
    departSearch.value = text;
  }
  departResults.value = [];
};

const setArriveeText = (text) => {
  if (arriveeSearch.value !== text) {
    skipArriveeSearch = true;
    arriveeSearch.value = text;
  }
  arriveeResults.value = [];
};

// ---------------------------------------------------------------------------
// Partage à distance (autre appareil)
// ---------------------------------------------------------------------------

const startRemoteSharing = async () => {
  // Le partage à distance et le partage local (ce même appareil) sont mutuellement exclusifs
  if (isLiveSharing.value) {
    stopLiveSharing();
  }

  isCreatingShareLink.value = true;
  try {
    const res = await fetch(route("position-share.create"), {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content ?? "",
      },
    });
    const data = await res.json();

    shareUrl.value = data.share_url;
    shareToken.value = data.token;
    isRemoteSharing.value = true;
    remoteSharingStatus.value = "waiting";
    hasCenteredOnRemote = false;

    remotePollingId = setInterval(pollRemotePosition, 3000);
  } catch (e) {
    routeError.value = "Impossible de créer le lien de partage.";
  } finally {
    isCreatingShareLink.value = false;
  }
};

const pollRemotePosition = async () => {
  if (!shareToken.value) return;

  try {
    const res = await fetch(`/api/position-share/${shareToken.value}`, {
      headers: { Accept: "application/json" },
    });

    if (res.status === 404) {
      remoteSharingStatus.value = "expired";
      stopRemoteSharing();
      return;
    }

    const data = await res.json();
    if (data.started && data.lat !== null && data.lng !== null) {
      remoteSharingStatus.value = "active";

      // Le nom du lieu n'est calculé qu'à la première position reçue.
      // Le drapeau est levé avant l'await pour éviter deux géocodages si les polls se chevauchent.
      const isFirstPosition = !hasCenteredOnRemote;
      let label = null;
      if (isFirstPosition) {
        hasCenteredOnRemote = true;
        label = await reverseGeocode(data.lat, data.lng);
      }

      // Le partage a pu être arrêté pendant le géocodage
      if (!isRemoteSharing.value) return;

      setDepartPosition(data.lat, data.lng, label, { live: true, remote: true });

      if (isFirstPosition) {
        map.setView([data.lat, data.lng], 15);
      }
    }
  } catch (e) {
    // On ignore les erreurs réseau ponctuelles : le prochain polling réessaiera
  }
};

const stopRemoteSharing = () => {
  if (remotePollingId) {
    clearInterval(remotePollingId);
    remotePollingId = null;
  }
  isRemoteSharing.value = false;
  shareUrl.value = "";
  shareToken.value = "";

  if (departMarker) {
    departMarker.setIcon(departIcon);
    departMarker.dragging.enable();
  }
};

const copyShareLink = async () => {
  try {
    await navigator.clipboard.writeText(shareUrl.value);
  } catch (e) {
    // Certains navigateurs/contexte non-HTTPS bloquent l'API clipboard ; l'utilisateur peut copier manuellement
  }
};

// ---------------------------------------------------------------------------
// Utilitaires
// ---------------------------------------------------------------------------

// Distance approx. en mètres entre 2 points (formule haversine simplifiée)
const distanceMeters = (lat1, lng1, lat2, lng2) => {
  const R = 6371000;
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLng = ((lng2 - lng1) * Math.PI) / 180;
  const a =
    Math.sin(dLat / 2) ** 2 +
    Math.cos((lat1 * Math.PI) / 180) *
      Math.cos((lat2 * Math.PI) / 180) *
      Math.sin(dLng / 2) ** 2;
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
};

const toNumber = (v) =>
  v === null || v === "" || v === undefined ? null : parseFloat(v);

// ---------------------------------------------------------------------------
// Marqueurs et itinéraire
// ---------------------------------------------------------------------------

const setDepartPosition = (
  lat,
  lng,
  label = null,
  { live = false, remote = false } = {}
) => {
  if (!map) return;

  // Toute position posée manuellement (recherche, clic, drag) interrompt les partages en cours
  if (!live && !remote) {
    if (isLiveSharing.value) stopLiveSharing();
    if (isRemoteSharing.value) stopRemoteSharing();
  }

  const showAsLive = live || remote;
  const icon = showAsLive ? liveIcon : departIcon;

  if (!departMarker) {
    departMarker = L.marker([lat, lng], { icon, draggable: true }).addTo(map);
    if (showAsLive) {
      departMarker.dragging.disable();
    }
    departMarker.on("dragend", async () => {
      const pos = departMarker.getLatLng();
      const dragLabel = await reverseGeocode(pos.lat, pos.lng);
      setDepartText(dragLabel);
      emit("update:depart", { lat: pos.lat, lng: pos.lng, label: dragLabel });
      recomputeRoute();
    });
  } else {
    departMarker.setLatLng([lat, lng]);
    departMarker.setIcon(icon);
    showAsLive ? departMarker.dragging.disable() : departMarker.dragging.enable();
  }

  if (label !== null) {
    setDepartText(label);
  }

  emit("update:depart", { lat, lng, label: label ?? departSearch.value });
  recomputeRoute(showAsLive);
};

const setArriveePosition = (lat, lng, label = null) => {
  if (!map) return;

  if (!arriveeMarker) {
    arriveeMarker = L.marker([lat, lng], { icon: arriveeIcon, draggable: true }).addTo(
      map
    );
    arriveeMarker.on("dragend", async () => {
      const pos = arriveeMarker.getLatLng();
      const dragLabel = await reverseGeocode(pos.lat, pos.lng);
      setArriveeText(dragLabel);
      emit("update:arrivee", { lat: pos.lat, lng: pos.lng, label: dragLabel });
      recomputeRoute();
    });
  } else {
    arriveeMarker.setLatLng([lat, lng]);
  }

  if (label !== null) {
    setArriveeText(label);
  }

  emit("update:arrivee", { lat, lng, label: label ?? arriveeSearch.value });
  recomputeRoute();
};

const recomputeRoute = async (isLiveUpdate = false) => {
  if (!departMarker || !arriveeMarker) return;

  const d = departMarker.getLatLng();

  // En direct : on ne relance OSRM que si on a bougé d'au moins 30m ou depuis 5s,
  // pour rester raisonnable vis-à-vis du serveur public OSRM.
  if (isLiveUpdate) {
    const now = Date.now();
    const movedEnough =
      !lastRouteComputePos ||
      distanceMeters(d.lat, d.lng, lastRouteComputePos.lat, lastRouteComputePos.lng) > 30;
    const enoughTimeElapsed = now - lastRouteComputeAt > 5000;

    if (!movedEnough && !enoughTimeElapsed) {
      return;
    }
    lastRouteComputeAt = now;
    lastRouteComputePos = { lat: d.lat, lng: d.lng };
  }

  const a = arriveeMarker.getLatLng();

  isRouting.value = true;
  routeError.value = "";

  try {
    const url = `https://router.project-osrm.org/route/v1/driving/${d.lng},${d.lat};${a.lng},${a.lat}?overview=full&geometries=geojson`;
    const res = await fetch(url);
    const data = await res.json();

    if (data.code === "Ok" && data.routes?.length) {
      const osrmRoute = data.routes[0];
      const distanceKm = osrmRoute.distance / 1000;
      emit("update:distance", Math.round(distanceKm * 100) / 100);

      if (routeLayer) {
        map.removeLayer(routeLayer);
      }
      routeLayer = L.geoJSON(osrmRoute.geometry, {
        style: { color: "#4f46e5", weight: 4, opacity: 0.8 },
      }).addTo(map);

      map.fitBounds(routeLayer.getBounds(), { padding: [40, 40] });
    } else {
      routeError.value = "Impossible de calculer l'itinéraire entre ces 2 points.";
    }
  } catch (e) {
    routeError.value = "Erreur réseau lors du calcul de l'itinéraire.";
  } finally {
    isRouting.value = false;
  }
};

// ---------------------------------------------------------------------------
// Recherche d'adresse (autocomplétion)
// ---------------------------------------------------------------------------

const searchAddress = async (query) => {
  if (!query || query.trim().length < 3) return [];

  try {
    const url = `https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&accept-language=fr&limit=5&q=${encodeURIComponent(
      query
    )}`;
    const res = await fetch(url, { headers: { Accept: "application/json" } });
    return await res.json();
  } catch (e) {
    return [];
  }
};

watch(departSearch, (value) => {
  clearTimeout(departSearchTimeout);
  if (skipDepartSearch) {
    skipDepartSearch = false;
    return;
  }
  departSearchTimeout = setTimeout(async () => {
    departResults.value = await searchAddress(value);
  }, 400);
});

watch(arriveeSearch, (value) => {
  clearTimeout(arriveeSearchTimeout);
  if (skipArriveeSearch) {
    skipArriveeSearch = false;
    return;
  }
  arriveeSearchTimeout = setTimeout(async () => {
    arriveeResults.value = await searchAddress(value);
  }, 400);
});

const chooseDepartResult = (result) => {
  departResults.value = [];
  setDepartPosition(parseFloat(result.lat), parseFloat(result.lon), shortName(result));
  map.setView([result.lat, result.lon], 14);
};

const chooseArriveeResult = (result) => {
  arriveeResults.value = [];
  setArriveePosition(parseFloat(result.lat), parseFloat(result.lon), shortName(result));
  map.setView([result.lat, result.lon], 14);
};

// ---------------------------------------------------------------------------
// Saisie manuelle des coordonnées GPS (départ uniquement)
// ---------------------------------------------------------------------------

const coordFormat = ref("decimal"); // 'decimal' | 'dms'
const coordLat = ref("");
const coordLng = ref("");
const coordError = ref("");
const isApplyingCoords = ref(false);

const coordPlaceholders = computed(() =>
  coordFormat.value === "decimal"
    ? { lat: "Latitude : -18.8792", lng: "Longitude : 47.5079" }
    : { lat: `Latitude : 18°52'45.1"S`, lng: `Longitude : 47°30'28.4"E` }
);

watch(coordFormat, () => {
  coordLat.value = "";
  coordLng.value = "";
  coordError.value = "";
});

const parseCoord = (value, axis) => {
  const max = axis === "lat" ? 90 : 180;
  const raw = (value ?? "").trim();
  if (!raw) return null;

  let result = null;

  if (coordFormat.value === "decimal") {
    const n = Number(raw.replace(",", "."));
    result = Number.isFinite(n) ? n : null;
  } else {
    // Ex : 18°52'45.1"S  |  47°30'28.4"E  (O accepté pour Ouest)
    const m = raw.match(
      /^(\d{1,3})\s*[°:\s]\s*(\d{1,2})\s*['′:\s]\s*(\d{1,2}(?:[.,]\d+)?)\s*["″]?\s*([NSEWO])$/i
    );
    if (!m) return null;

    const [, d, min, sec, hemi] = m;
    const h = hemi.toUpperCase();
    if (axis === "lat" && !"NS".includes(h)) return null;
    if (axis === "lng" && !"EWO".includes(h)) return null;
    if (Number(min) >= 60 || Number(sec.replace(",", ".")) >= 60) return null;

    const dec = Number(d) + Number(min) / 60 + Number(sec.replace(",", ".")) / 3600;
    result = h === "S" || h === "W" || h === "O" ? -dec : dec;
  }

  return result !== null && Math.abs(result) <= max ? result : null;
};

const applyCoords = async () => {
  coordError.value = "";
  const lat = parseCoord(coordLat.value, "lat");
  const lng = parseCoord(coordLng.value, "lng");

  if (lat === null || lng === null) {
    coordError.value =
      coordFormat.value === "decimal"
        ? "Coordonnées invalides. Latitude entre -90 et 90, longitude entre -180 et 180."
        : `Format DMS invalide. Exemple : 18°52'45.1"S et 47°30'28.4"E.`;
    return;
  }

  isApplyingCoords.value = true;
  try {
    const label = await reverseGeocode(lat, lng);
    setDepartPosition(lat, lng, label); // coupe aussi les partages en cours

    // Si l'arrivée existe, recomputeRoute a déjà ajusté la vue
    if (!arriveeMarker) map.setView([lat, lng], 15);
  } finally {
    isApplyingCoords.value = false;
  }
};

// ---------------------------------------------------------------------------
// Partage de position de ce téléphone
// ---------------------------------------------------------------------------

const startLiveSharing = () => {
  if (!navigator.geolocation) {
    routeError.value = "La géolocalisation n'est pas disponible sur cet appareil.";
    return;
  }

  if (isRemoteSharing.value) {
    stopRemoteSharing();
  }

  isLocating.value = true;
  isLiveSharing.value = true;
  let hasCentered = false;

  liveWatchId = navigator.geolocation.watchPosition(
    async (position) => {
      const { latitude, longitude } = position.coords;
      isLocating.value = false;

      // Reverse-géocodage une seule fois au démarrage (pas à chaque mise à jour GPS,
      // pour respecter l'usage raisonnable de l'API Nominatim).
      // Le drapeau est levé avant l'await pour éviter plusieurs géocodages simultanés.
      if (!hasCentered) {
        hasCentered = true;
        const label = await reverseGeocode(latitude, longitude);

        // Le partage a pu être arrêté pendant le géocodage
        if (!isLiveSharing.value) return;

        setDepartPosition(latitude, longitude, label, { live: true });
        map.setView([latitude, longitude], 15);
      } else {
        setDepartPosition(latitude, longitude, null, { live: true });
      }
    },
    () => {
      routeError.value = "Position refusée ou indisponible.";
      isLocating.value = false;
      isLiveSharing.value = false;
    },
    { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 }
  );
};

const stopLiveSharing = () => {
  if (liveWatchId !== null) {
    navigator.geolocation.clearWatch(liveWatchId);
    liveWatchId = null;
  }
  isLiveSharing.value = false;

  // On repasse sur l'icône statique (déplaçable) une fois le partage arrêté
  if (departMarker) {
    departMarker.setIcon(departIcon);
    departMarker.dragging.enable();
  }
};

const toggleLiveSharing = () => {
  if (isLiveSharing.value) {
    stopLiveSharing();
  } else {
    startLiveSharing();
  }
};

// ---------------------------------------------------------------------------
// Cycle de vie
// ---------------------------------------------------------------------------

onMounted(() => {
  map = L.map(mapContainer.value).setView(DEFAULT_CENTER, DEFAULT_ZOOM);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution:
      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
  }).addTo(map);

  map.on("click", async (e) => {
    const { lat, lng } = e.latlng;
    const label = await reverseGeocode(lat, lng);

    if (activeTarget.value === "depart") {
      setDepartPosition(lat, lng, label);
    } else {
      setArriveePosition(lat, lng, label);
    }
  });

  const initDepartLat = toNumber(props.departLat);
  const initDepartLng = toNumber(props.departLng);
  const initArriveeLat = toNumber(props.arriveeLat);
  const initArriveeLng = toNumber(props.arriveeLng);

  if (initDepartLat !== null && initDepartLng !== null) {
    setDepartPosition(initDepartLat, initDepartLng, props.departLabel);
  }
  if (initArriveeLat !== null && initArriveeLng !== null) {
    setArriveePosition(initArriveeLat, initArriveeLng, props.arriveeLabel);
  }
});

onBeforeUnmount(() => {
  clearTimeout(departSearchTimeout);
  clearTimeout(arriveeSearchTimeout);
  if (liveWatchId !== null) {
    navigator.geolocation.clearWatch(liveWatchId);
  }
  if (remotePollingId) {
    clearInterval(remotePollingId);
  }
  if (map) {
    map.remove();
    map = null;
  }
});
</script>

<template>
  <div class="space-y-4">
    <!-- Recherche départ -->
    <div class="relative">
      <label class="block text-sm font-medium text-gray-700 mb-1">Lieu de départ</label>
      <div class="flex gap-2 flex-wrap">
        <input
          v-model="departSearch"
          type="text"
          placeholder="Rechercher une adresse ou un lieu..."
          class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
        />
        <button
          type="button"
          @click="toggleLiveSharing"
          :disabled="isLocating"
          :class="[
            'whitespace-nowrap px-3 py-2 text-sm rounded-md disabled:opacity-50 flex items-center gap-1.5',
            isLiveSharing
              ? 'bg-red-50 text-red-700 hover:bg-red-100'
              : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100',
          ]"
        >
          <span v-if="isLiveSharing" class="relative flex h-2.5 w-2.5">
            <span
              class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"
            ></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
          </span>
          {{
            isLocating
              ? "Localisation..."
              : isLiveSharing
              ? "Arrêter le partage"
              : "📡 Ce téléphone"
          }}
        </button>

        <button
          v-if="!isRemoteSharing"
          type="button"
          @click="startRemoteSharing"
          :disabled="isCreatingShareLink"
          class="whitespace-nowrap px-3 py-2 text-sm bg-emerald-50 text-emerald-700 rounded-md hover:bg-emerald-100 disabled:opacity-50"
        >
          {{ isCreatingShareLink ? "Création du lien..." : "📱 Autre appareil" }}
        </button>
        <button
          v-else
          type="button"
          @click="stopRemoteSharing"
          class="whitespace-nowrap px-3 py-2 text-sm bg-red-50 text-red-700 rounded-md hover:bg-red-100"
        >
          Arrêter l'écoute
        </button>
      </div>
      <ul
        v-if="departResults.length"
        class="absolute z-[1000] w-full bg-white border border-gray-200 rounded-md shadow-lg mt-1 max-h-48 overflow-y-auto"
      >
        <li
          v-for="result in departResults"
          :key="result.place_id"
          @click="chooseDepartResult(result)"
          :title="result.display_name"
          class="px-3 py-2 text-sm hover:bg-indigo-50 cursor-pointer truncate"
        >
          {{ shortName(result) }}
        </li>
      </ul>
    </div>

    <!-- Saisie des coordonnées GPS du véhicule -->
    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg space-y-2">
      <div class="flex items-center gap-4 text-sm flex-wrap">
        <span class="font-medium text-gray-700">Coordonnées GPS du véhicule</span>
        <label class="flex items-center gap-1">
          <input
            type="radio"
            v-model="coordFormat"
            value="decimal"
            class="text-indigo-600"
          />
          Décimal
        </label>
        <label class="flex items-center gap-1">
          <input type="radio" v-model="coordFormat" value="dms" class="text-indigo-600" />
          DMS
        </label>
      </div>

      <div class="flex gap-2 flex-wrap">
        <input
          v-model="coordLat"
          type="text"
          :placeholder="coordPlaceholders.lat"
          @keyup.enter="applyCoords"
          class="flex-1 min-w-[150px] text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
        />
        <input
          v-model="coordLng"
          type="text"
          :placeholder="coordPlaceholders.lng"
          @keyup.enter="applyCoords"
          class="flex-1 min-w-[150px] text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
        />
        <button
          type="button"
          @click="applyCoords"
          :disabled="isApplyingCoords"
          class="whitespace-nowrap px-3 py-2 text-sm bg-indigo-50 text-indigo-700 rounded-md hover:bg-indigo-100 disabled:opacity-50"
        >
          {{ isApplyingCoords ? "Recherche..." : "📍 Placer sur la carte" }}
        </button>
      </div>

      <p v-if="coordError" class="text-sm text-red-600">{{ coordError }}</p>
    </div>

    <!-- Panneau de partage distant (lien + QR code) -->
    <div
      v-if="isRemoteSharing"
      class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg space-y-3"
    >
      <div class="flex items-center gap-2">
        <span v-if="remoteSharingStatus === 'waiting'" class="text-sm text-emerald-800">
          ⏳ En attente que le conducteur ouvre le lien et démarre le partage...
        </span>
        <span
          v-else-if="remoteSharingStatus === 'active'"
          class="flex items-center gap-2 text-sm text-emerald-800 font-medium"
        >
          <span class="relative flex h-2.5 w-2.5">
            <span
              class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"
            ></span>
            <span
              class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"
            ></span>
          </span>
          Position reçue en direct
        </span>
        <span v-else class="text-sm text-red-700">
          Le lien a expiré, relancez un partage.
        </span>
      </div>

      <div class="flex flex-col sm:flex-row items-center gap-4">
        <img
          :src="`https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=${encodeURIComponent(
            shareUrl
          )}`"
          alt="QR code du lien de partage"
          class="rounded-md border border-emerald-200 bg-white p-1"
          width="140"
          height="140"
        />
        <div class="flex-1 w-full">
          <p class="text-xs text-gray-600 mb-1">
            À envoyer au conducteur (SMS, WhatsApp...) ou à scanner :
          </p>
          <div class="flex gap-2">
            <input
              :value="shareUrl"
              readonly
              class="flex-1 text-xs border-gray-300 rounded-md bg-white"
            />
            <button
              type="button"
              @click="copyShareLink"
              class="px-3 py-1.5 text-xs bg-white border border-emerald-300 text-emerald-700 rounded-md hover:bg-emerald-100"
            >
              Copier
            </button>
          </div>
          <p class="text-xs text-gray-500 mt-2">Le lien expire après 2h.</p>
        </div>
      </div>
    </div>

    <!-- Recherche arrivée -->
    <div class="relative">
      <label class="block text-sm font-medium text-gray-700 mb-1">Lieu d'arrivée</label>
      <input
        v-model="arriveeSearch"
        type="text"
        placeholder="Rechercher une adresse ou un lieu..."
        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
      />
      <ul
        v-if="arriveeResults.length"
        class="absolute z-[1000] w-full bg-white border border-gray-200 rounded-md shadow-lg mt-1 max-h-48 overflow-y-auto"
      >
        <li
          v-for="result in arriveeResults"
          :key="result.place_id"
          @click="chooseArriveeResult(result)"
          :title="result.display_name"
          class="px-3 py-2 text-sm hover:bg-indigo-50 cursor-pointer truncate"
        >
          {{ shortName(result) }}
        </li>
      </ul>
    </div>

    <!-- Mode de clic sur la carte -->
    <div class="flex items-center gap-4 text-sm">
      <span class="text-gray-600">Cliquer sur la carte pour placer :</span>
      <label class="flex items-center gap-1">
        <input
          type="radio"
          v-model="activeTarget"
          value="depart"
          class="text-indigo-600"
        />
        <span class="text-blue-700 font-medium">Départ</span>
      </label>
      <label class="flex items-center gap-1">
        <input
          type="radio"
          v-model="activeTarget"
          value="arrivee"
          class="text-indigo-600"
        />
        <span class="text-red-700 font-medium">Arrivée</span>
      </label>
    </div>

    <!-- Carte -->
    <div
      ref="mapContainer"
      class="w-full h-96 rounded-lg border border-gray-300 z-0"
    ></div>

    <p v-if="isLiveSharing" class="text-sm text-red-600 flex items-center gap-1.5">
      <span class="relative flex h-2 w-2">
        <span
          class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"
        ></span>
        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
      </span>
      Position en direct — le point de départ suit vos déplacements.
    </p>
    <p v-if="isRouting" class="text-sm text-indigo-600">Calcul de l'itinéraire...</p>
    <p v-if="routeError" class="text-sm text-red-600">{{ routeError }}</p>
  </div>
</template>

<style>
/* Non scoped : ces classes sont injectées dynamiquement par Leaflet (L.divIcon),
   en dehors du template compilé par Vue, donc un style "scoped" ne s'appliquerait pas. */
.live-position-marker {
  position: relative;
  width: 22px;
  height: 22px;
}
.live-position-marker__dot {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 14px;
  height: 14px;
  margin: -7px 0 0 -7px;
  background: #4285f4;
  border: 2px solid #fff;
  border-radius: 50%;
  box-shadow: 0 0 4px rgba(0, 0, 0, 0.4);
}
.live-position-marker__pulse {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 22px;
  height: 22px;
  margin: -11px 0 0 -11px;
  background: rgba(66, 133, 244, 0.4);
  border-radius: 50%;
  animation: live-position-pulse 1.6s infinite ease-out;
}
@keyframes live-position-pulse {
  0% {
    transform: scale(0.5);
    opacity: 1;
  }
  100% {
    transform: scale(2.2);
    opacity: 0;
  }
}
</style>