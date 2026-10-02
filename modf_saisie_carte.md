# Modifications — Saisie sur carte (nouveau trajet)

## 1. Correction : champs lieu non remplis au clic / drag

**Problème :** au clic sur la carte ou au déplacement d'un marqueur, les champs « lieu de départ / arrivée » n'étaient pas remplis.

**Cause :** `label` valait `null` dans ces cas, donc l'ancien texte restait. Seules la recherche, le partage en direct et le partage à distance utilisaient le géocodage.

**Changements :**

1. Nouvelle fonction `reverseGeocode(lat, lng)` : appelle Nominatim `/reverse` et retourne `display_name` (repli : `lat, lng`).
2. `map.on('click')` devient `async` : calcule le label avant d'appeler `setDepartPosition` / `setArriveePosition`.
3. `dragend` (départ et arrivée) : calcul du label, mise à jour de `departSearch` / `arriveeSearch`, puis `emit` avec `{ lat, lng, label }`.

## 2. Nouveau : saisie des coordonnées GPS du véhicule (départ uniquement)

**Fonctionnement :**

- Choix du format via boutons radio : **Décimal** ou **DMS** (un seul actif à la fois ; changer de format vide les champs).
- Deux champs : latitude et longitude, avec bouton « 📍 Placer sur la carte » (ou touche Entrée).
- Le point devient le **départ** : marqueur placé, nom du lieu rempli par `reverseGeocode`, événement `update:depart` émis, itinéraire recalculé si l'arrivée existe.
- Un partage en cours (ce téléphone / autre appareil) est interrompu.

**Formats acceptés :**

| Format | Exemple latitude | Exemple longitude |
| --- | --- | --- |
| Décimal | `-18.8792` (virgule acceptée) | `47.5079` |
| DMS | `18°52'45.1"S` | `47°30'28.4"E` (O = Ouest accepté) |

**Validation :** latitude entre -90 et 90, longitude entre -180 et 180, minutes et secondes < 60, hémisphère cohérent avec l'axe (N/S pour la latitude, E/W/O pour la longitude).

**Ajouts au code :**

- Import de `computed`.
- Refs : `coordFormat`, `coordLat`, `coordLng`, `coordError`, `isApplyingCoords`.
- Fonctions : `parseCoord`, `applyCoords`, `watch(coordFormat)`, `computed coordPlaceholders`.
- Template : bloc « Coordonnées GPS du véhicule » placé après la recherche du départ.

## Points d'attention

- Vider `departResults` / `arriveeResults` après l'affectation d'un label, pour éviter l'ouverture parasite des suggestions (les `watch` relancent une recherche).
- Nominatim : 1 requête/seconde maximum.
- Le formulaire parent doit écouter `@update:depart` et `@update:arrivee` et copier `label` dans ses champs.