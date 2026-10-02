<script setup>
import { ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  vehicule: Object,
  lastOdometer: Number,
  fuelType: String,
  currentFuelPrice: Object,
  consumptionAnalysis: Object,
});

const form = useForm({
  vehicule_id: props.vehicule.id,
  chauffeur_name: "",
  ravitaillement_date: new Date().toISOString().split("T")[0],
  station_service: "",
  liters_purchased: "",
  price_per_liter: props.currentFuelPrice?.price || "",
  amount_paid: "",
  odo_station: props.lastOdometer || "",
  payment_method: "cash",
  receipt_number: "",
  notes: "",
  is_custom_price: false,
});

const officialPrice = ref(props.currentFuelPrice?.price || null);
const isPriceModified = ref(false);

const showAdvice = ref(false);

// État pour le chargement des calculs
const isCalculating = ref(false);
let calculationTimeout = null;

// Fonction pour debounce les calculs
const debounceCalculation = (callback, delay = 500) => {
  isCalculating.value = true;

  if (calculationTimeout) {
    clearTimeout(calculationTimeout);
  }

  calculationTimeout = setTimeout(() => {
    callback();
    isCalculating.value = false;
  }, delay);
};

// Watch for changes in liters_purchased to auto-calculate amount_paid
watch(
  () => form.liters_purchased,
  (newVal) => {
    if (newVal && form.price_per_liter && parseFloat(newVal) > 0) {
      debounceCalculation(() => {
        const calculated = (
          parseFloat(newVal) * parseFloat(form.price_per_liter)
        ).toFixed(2);
        form.amount_paid = calculated;
      });
    }
  }
);

// Watch for changes in amount_paid to auto-calculate liters_purchased
watch(
  () => form.amount_paid,
  (newVal) => {
    if (newVal && form.price_per_liter && parseFloat(form.price_per_liter) > 0) {
      debounceCalculation(() => {
        const calculated = (
          parseFloat(newVal) / parseFloat(form.price_per_liter)
        ).toFixed(2);
        form.liters_purchased = calculated;
      });
    }
  }
);

// Watch for changes in price_per_liter to recalculate and detect custom price
watch(
  () => form.price_per_liter,
  (newVal) => {
    if (newVal && parseFloat(newVal) > 0) {
      // Check if price was modified from official price
      if (officialPrice.value && parseFloat(newVal) !== parseFloat(officialPrice.value)) {
        isPriceModified.value = true;
        form.is_custom_price = true;
      } else {
        isPriceModified.value = false;
        form.is_custom_price = false;
      }

      // Recalculate based on what field has value
      debounceCalculation(() => {
        if (form.liters_purchased) {
          form.amount_paid = (
            parseFloat(form.liters_purchased) * parseFloat(newVal)
          ).toFixed(2);
        } else if (form.amount_paid) {
          form.liters_purchased = (
            parseFloat(form.amount_paid) / parseFloat(newVal)
          ).toFixed(2);
        }
      });
    }
  }
);

// Computed values for display
const totalCost = computed(() => {
  if (form.liters_purchased && form.price_per_liter) {
    return (parseFloat(form.liters_purchased) * parseFloat(form.price_per_liter)).toFixed(
      2
    );
  }
  return "0.00";
});

const totalLiters = computed(() => {
  if (form.amount_paid && form.price_per_liter && parseFloat(form.price_per_liter) > 0) {
    return (parseFloat(form.amount_paid) / parseFloat(form.price_per_liter)).toFixed(2);
  }
  return "0.00";
});

const consumptionRate = computed(() => {
  if (props.vehicule.average_consumption) {
    return parseFloat(props.vehicule.average_consumption).toFixed(2);
  }
  return null;
});

// Estimated range based on fuel and consumption
const estimatedRange = computed(() => {
  if (
    consumptionRate.value &&
    form.liters_purchased &&
    parseFloat(form.liters_purchased) > 0
  ) {
    const liters = parseFloat(form.liters_purchased);
    const consumption = parseFloat(consumptionRate.value);
    if (consumption > 0) {
      return ((liters / consumption) * 100).toFixed(0);
    }
  }
  return null;
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("fr-FR");
};

const resetToOfficialPrice = () => {
  if (officialPrice.value) {
    form.price_per_liter = officialPrice.value;
    isPriceModified.value = false;
    form.is_custom_price = false;
  }
};

const submit = () => {
  form.post(route("ravitaillements.store"), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout title="Nouveau Carburant">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nouveau Carburant</h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <!-- Vehicle Info -->
          <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="font-semibold text-lg mb-2">Véhicule sélectionné</h3>
            <p class="text-gray-700">
              <span class="font-medium">{{ vehicule.alias }}</span>
              <span class="text-gray-500 ml-2"
                >{{ vehicule.make }} - {{ vehicule.license_plate }}</span
              >
            </p>
            <p class="text-sm text-gray-600 mt-1">
              Type de carburant: <span class="font-medium">{{ fuelType }}</span>
            </p>
          </div>

          <!-- Current Fuel Price Info -->
          <div
            v-if="currentFuelPrice"
            class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg"
          >
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <svg
                  class="h-5 w-5 text-green-400"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-green-800">
                  Prix actuel du carburant
                </h3>
                <div class="mt-2 text-sm text-green-700">
                  <p class="font-semibold text-lg">
                    {{
                      parseFloat(currentFuelPrice.price).toLocaleString("fr-FR", {
                        minimumFractionDigits: 2,
                      })
                    }}
                    Ar/L
                  </p>
                  <p class="text-xs mt-1">
                    Effectif depuis le {{ formatDate(currentFuelPrice.effective_date) }}
                  </p>
                  <p v-if="currentFuelPrice.notes" class="text-xs mt-1 italic">
                    {{ currentFuelPrice.notes }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <form @submit.prevent="submit" class="space-y-6">
            <!-- Driver Name -->
            <div>
              <InputLabel for="chauffeur_name" value="Nom du conducteur" />
              <TextInput
                id="chauffeur_name"
                v-model="form.chauffeur_name"
                type="text"
                class="mt-1 block w-full"
                placeholder="Nom du conducteur (optionnel)"
              />
              <InputError :message="form.errors.chauffeur_name" class="mt-2" />
            </div>

            <!-- Date and Station -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <InputLabel
                  for="ravitaillement_date"
                  value="Date de ravitaillement du Carburant"
                />
                <TextInput
                  id="ravitaillement_date"
                  v-model="form.ravitaillement_date"
                  type="date"
                  class="mt-1 block w-full"
                  required
                  :max="new Date().toISOString().split('T')[0]"
                />
                <InputError :message="form.errors.ravitaillement_date" class="mt-2" />
              </div>

              <div>
                <InputLabel for="station_service" value="Station-service" />
                <TextInput
                  id="station_service"
                  v-model="form.station_service"
                  type="text"
                  class="mt-1 block w-full"
                  required
                  placeholder="Ex: Total, Shell, Jirama..."
                />
                <InputError :message="form.errors.station_service" class="mt-2" />
              </div>
            </div>

            <!-- Fuel Details with Auto-calculation -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <InputLabel for="liters_purchased" value="Litres achetés" />
                <TextInput
                  id="liters_purchased"
                  v-model="form.liters_purchased"
                  type="number"
                  step="0.01"
                  min="0.01"
                  class="mt-1 block w-full"
                  placeholder="Ex: 45.5"
                />
                <InputError :message="form.errors.liters_purchased" class="mt-2" />
                <p class="mt-1 text-xs text-gray-500">
                  💡 Calculé automatiquement si vous saisissez le montant
                </p>
              </div>

              <div>
                <InputLabel for="price_per_liter" value="Prix par litre (Ar)" />
                <div class="relative">
                  <TextInput
                    id="price_per_liter"
                    v-model="form.price_per_liter"
                    type="number"
                    step="0.01"
                    min="0.01"
                    class="mt-1 block w-full"
                    :class="{ 'border-orange-300': isPriceModified }"
                    required
                    placeholder="Prix par litre"
                  />
                  <div
                    v-if="currentFuelPrice && !isPriceModified"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                  >
                    <svg
                      class="h-5 w-5 text-green-500"
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </div>
                </div>
                <InputError :message="form.errors.price_per_liter" class="mt-2" />
                <p
                  v-if="currentFuelPrice && !isPriceModified"
                  class="mt-1 text-xs text-green-600"
                >
                  🔒 Prix officiel appliqué
                </p>
                <div v-if="isPriceModified" class="mt-1">
                  <p class="text-xs text-orange-600 mb-1">
                    ⚠️ Prix personnalisé (différent du prix officiel)
                  </p>
                  <button
                    type="button"
                    @click="resetToOfficialPrice"
                    class="text-xs text-blue-600 hover:text-blue-800 underline"
                  >
                    Revenir au prix officiel
                  </button>
                </div>
              </div>

              <div>
                <InputLabel for="amount_paid" value="Montant payé (Ar)" />
                <TextInput
                  id="amount_paid"
                  v-model="form.amount_paid"
                  type="number"
                  step="0.01"
                  min="0.01"
                  class="mt-1 block w-full"
                  placeholder="Ex: 227500"
                />
                <InputError :message="form.errors.amount_paid" class="mt-2" />
                <p class="mt-1 text-xs text-gray-500">
                  💡 Calculé automatiquement si vous saisissez les litres
                </p>
              </div>
            </div>

            <!-- Calculated Values Display with Loading State -->
            <div
              class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200"
            >
              <h4 class="font-semibold mb-3 text-indigo-900">
                📊 Calculs automatiques
                <!-- Loading Spinner -->
                <svg
                  v-if="isCalculating"
                  class="animate-spin ml-2 h-4 w-4 text-indigo-600"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-3 rounded-md shadow-sm">
                  <span class="text-gray-600 text-sm">Coût total:</span>
                  <div class="text-2xl font-bold text-indigo-600 mt-1">
                    {{
                      parseFloat(totalCost).toLocaleString("fr-FR", {
                        minimumFractionDigits: 2,
                      })
                    }}
                    Ar
                  </div>
                </div>
                <div class="bg-white p-3 rounded-md shadow-sm">
                  <span class="text-gray-600 text-sm">Total litres:</span>
                  <div class="text-2xl font-bold text-green-600 mt-1">
                    {{
                      parseFloat(totalLiters).toLocaleString("fr-FR", {
                        minimumFractionDigits: 2,
                      })
                    }}
                    L
                  </div>
                </div>
              </div>
            </div>

            <!-- Info Box for Auto-calculation -->
            <button
              type="button"
              @click="showAdvice = !showAdvice"
              class="flex items-center gap-2 text-indigo-600 hover:text-indigo-800 transition-colors"
            >
              <div class="flex-shrink-0">
                  <svg
                    class="h-5 w-5 text-blue-400"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>

              <h3 class="text-sm font-medium text-blue-800">
                    Astuces pour l'entrée des données
                  </h3>
            </button>
            <div v-if="showAdvice" class="p-4 bg-blue-50 rounded-lg border border-blue-200">
              <div class="flex">
                
                <div class="ml-3">
                  
                  <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                      <li>
                        Entrez soit les <strong>litres achetés</strong> ou le
                        <strong>montant payé</strong>, l'autre sera calculé
                        automatiquement
                      </li>
                      <li>
                        Le <strong>prix par litre</strong> est pré-rempli avec le prix
                        officiel actuel
                      </li>
                      <li>
                        Vous pouvez modifier le <strong>prix par litre</strong> si
                        différent du prix officiel
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Odometer Reading -->
            <div>
              <InputLabel for="odo_station" value="Kilométrage à la station" />
              <TextInput
                id="odo_station"
                v-model="form.odo_station"
                type="number"
                step="0.01"
                min="0"
                class="mt-1 block w-full"
                placeholder="Auto-rempli si vide"
              />
              <InputError :message="form.errors.odo_station" class="mt-2" />
              <p class="mt-1 text-xs text-gray-500">
                Dernier relevé: {{ lastOdometer }} km
              </p>
            </div>

            <!-- 🚀 Consumption Info - AMÉLIORE -->
            <div
              v-if="consumptionRate"
              class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200"
            >
              <h4 class="font-semibold mb-3 text-green-900">
                ⛽ Consommation du véhicule
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-3 rounded-md shadow-sm">
                  <span class="text-gray-600 text-sm">Consommation moyenne:</span>
                  <div class="text-2xl font-bold text-green-700 mt-1">
                    {{ consumptionRate }} L/100km
                  </div>
                </div>
                <div v-if="estimatedRange" class="bg-white p-3 rounded-md shadow-sm">
                  <span class="text-gray-600 text-sm">Autonomie estimée:</span>
                  <div class="text-2xl font-bold text-blue-700 mt-1 flex items-center">
                    ~{{ estimatedRange }} km
                    <svg
                      class="w-5 h-5 ml-2 text-blue-500"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                      />
                    </svg>
                  </div>
                </div>
              </div>
              <p class="text-xs text-green-600 mt-2">
                💡 Basé sur {{ form.liters_purchased || 0 }} L et une consommation de
                {{ consumptionRate }} L/100km
              </p>
            </div>

            <!-- Affichage de la consommation estimée -->
            <div
              v-if="consumptionAnalysis"
              class="mb-6 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg"
            >
              <div class="flex items-center justify-between">
                <div>
                  <h4 class="font-semibold text-blue-900 mb-1">Consommation actuelle</h4>
                  <ConsumptionBadge
                    :consumption="consumptionAnalysis.consumption"
                    :precision="consumptionAnalysis.precision"
                    :method="consumptionAnalysis.method"
                    :overconsumption-alert="consumptionAnalysis.overconsumption_alert"
                    :vehicule-id="vehicule.id"
                  />
                </div>
              </div>
            </div>

            <!-- Payment Method -->
            <div>
              <InputLabel for="payment_method" value="Mode de paiement" />
              <select
                id="payment_method"
                v-model="form.payment_method"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required
              >
                <option value="cash">Espèces</option>
                <option value="carte">Carte bancaire</option>
                <option value="virement">Virement bancaire</option>
                <option value="mobile">Mobile Money</option>
              </select>
              <InputError :message="form.errors.payment_method" class="mt-2" />
            </div>

            <!-- Receipt Number -->
            <div>
              <InputLabel for="receipt_number" value="Numéro de reçu (optionnel)" />
              <TextInput
                id="receipt_number"
                v-model="form.receipt_number"
                type="text"
                class="mt-1 block w-full"
                placeholder="Ex: REC-2024-001"
              />
              <InputError :message="form.errors.receipt_number" class="mt-2" />
            </div>

            <!-- Notes -->
            <div>
              <InputLabel for="notes" value="Remarques" />
              <textarea
                id="notes"
                v-model="form.notes"
                rows="3"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                placeholder="Notes additionnelles..."
              ></textarea>
              <InputError :message="form.errors.notes" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4">
              <a
                :href="route('ravitaillements.index')"
                class="text-gray-600 hover:text-gray-900"
              >
                Annuler
              </a>
              <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                Enregistrer
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
