<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    maintenance: Object,
    historique: Array,
});

// Calcul du coût total des pièces
const coutTotalPieces = computed(() => {
    if (!props.maintenance.pieces || props.maintenance.pieces.length === 0) {
        return 0;
    }
    return props.maintenance.pieces.reduce((total, piece) => {
        return total + (parseFloat(piece.prix_total) || 0);
    }, 0);
});

// Calcul du coût total global
const coutTotalGlobal = computed(() => {
    const mainOeuvre = parseFloat(props.maintenance.cout_main_oeuvre) || 0;
    return mainOeuvre + coutTotalPieces.value;
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value) + ' Ar';
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getPieceStatusBadge = (piece) => {
    const limite = parseFloat(piece.limite_utilisation) || 1;
    const restant = parseFloat(piece.potentiel_restant) || 0;
    const pourcentage = (restant / limite) * 100;

    if (pourcentage <= 0) {
        return { class: 'bg-red-100 text-red-800 border border-red-200', label: '❌ Expiré' };
    } else if (pourcentage < 20) {
        return { class: 'bg-orange-100 text-orange-800 border border-orange-200', label: '⚠️ Critique' };
    } else if (pourcentage < 50) {
        return { class: 'bg-yellow-100 text-yellow-800 border border-yellow-200', label: '⚡ Attention' };
    } else {
        return { class: 'bg-green-100 text-green-800 border border-green-200', label: '✅ Bon' };
    }
};

const getUniteLabel = (unite) => {
    const labels = {
        km: 'km',
        heures: 'h',
        cycles: 'cycles',
        tours: 'tours',
        jours: 'jours',
        mois: 'mois',
        annees: 'ans',
    };
    return labels[unite] || unite;
};

const getEtatLabel = (etat) => {
    return etat === 'neuf' ? '✨ Neuf' : '♻️ Occasion';
};

const getPourcentageRestant = (piece) => {
    const limite = parseFloat(piece.limite_utilisation) || 1;
    const restant = parseFloat(piece.potentiel_restant) || 0;
    return ((restant / limite) * 100).toFixed(1);
};
</script>

<template>
    <AppLayout title="Détails de la Maintenance">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Détails de la Maintenance
                </h2>
                <div class="flex gap-3">
                    <Link
                        v-if="!maintenance.validated_at"
                        :href="route('maintenances.edit', maintenance.id)"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition-colors"
                    >
                        ✏️ Modifier
                    </Link>
                    <Link
                        :href="route('maintenances.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition-colors"
                    >
                        ← Retour
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-indigo-600 to-blue-800 text-white shadow-xl rounded-xl overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-4xl">🔧</span>
                                    <div>
                                        <h3 class="text-3xl font-bold">
                                            {{ maintenance.reference }}
                                        </h3>
                                        <p class="text-blue-100 text-lg mt-1">
                                            {{ maintenance.nature_intervention }}
                                        </p>
                                    </div>
                                </div>
                                <p class="text-blue-100 mt-3">
                                    📅 {{ formatDate(maintenance.date_debut) }}
                                    <span v-if="maintenance.date_fin"> → {{ formatDate(maintenance.date_fin) }}</span>
                                </p>
                            </div>
                            <div class="text-right">
                                <!--<div v-if="maintenance.validated_at" class="inline-flex px-4 py-2 rounded-lg text-sm font-semibold bg-green-500 text-white mb-3">
                                    ✅ Validée
                                </div>
                                <div v-else class="inline-flex px-4 py-2 rounded-lg text-sm font-semibold bg-yellow-500 text-white mb-3">
                                    ⏳ En attente
                                </div>-->
                                <div class="text-4xl font-bold">
                                    {{ formatCurrency(coutTotalGlobal) }}
                                </div>
                                <div class="text-sm text-blue-100 mt-1">
                                    Coût total
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle and Garage Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <span class="text-2xl">🚗</span>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Véhicule</h4>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Modèle</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ maintenance.vehicule.make }} {{ maintenance.vehicule.model }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Plaque</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ maintenance.vehicule.license_plate }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Kilométrage</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ maintenance.kilometrage_actuel }} km
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                <span class="text-2xl">🔧</span>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Garage</h4>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Nom</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ maintenance.garage_nom }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">📍 Lieu</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ maintenance.garage_lieu }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">📞 Contact</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ maintenance.garage_contact }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Responsables Info -->
                <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <span class="text-xl">👤</span>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Responsables</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-xs text-blue-600 font-medium mb-1">Créé par</p>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ maintenance.user.name }}
                            </p>
                        </div>
                        <div v-if="maintenance.validateur" class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <p class="text-xs text-green-600 font-medium mb-1">Validé par</p>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ maintenance.validateur.name }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Le {{ new Date(maintenance.validated_at).toLocaleString('fr-FR') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Cost Breakdown -->
                <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-6">💰 Détails des coûts</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                            <div class="text-3xl mb-2">🔧</div>
                            <div class="text-sm text-blue-700 font-medium mb-2">Main d'œuvre</div>
                            <div class="text-2xl font-bold text-blue-900">
                                {{ formatCurrency(maintenance.cout_main_oeuvre) }}
                            </div>
                        </div>
                        <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
                            <div class="text-3xl mb-2">🔩</div>
                            <div class="text-sm text-green-700 font-medium mb-2">Pièces ({{ maintenance.pieces?.length || 0 }})</div>
                            <div class="text-2xl font-bold text-green-900">
                                {{ formatCurrency(coutTotalPieces) }}
                            </div>
                        </div>
                        <div class="text-center p-6 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl border-2 border-indigo-300">
                            <div class="text-3xl mb-2">💵</div>
                            <div class="text-sm text-indigo-700 font-medium mb-2">Total</div>
                            <div class="text-3xl font-bold text-indigo-900">
                                {{ formatCurrency(coutTotalGlobal) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pieces -->
                <div v-if="maintenance.pieces && maintenance.pieces.length > 0" class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <span class="text-xl">🔩</span>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">
                            Pièces remplacées / inspectées ({{ maintenance.pieces.length }})
                        </h4>
                    </div>
                    <div class="space-y-4">
                        <div v-for="piece in maintenance.pieces" :key="piece.id" class="border-2 border-gray-200 rounded-xl p-5 hover:shadow-lg transition-all duration-200">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h5 class="font-semibold text-gray-900 text-lg">{{ piece.nom_piece }}</h5>
                                        <span class="text-xs px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-medium border border-blue-200">
                                            {{ piece.marque_piece }}
                                        </span>
                                        <span class="text-xs px-3 py-1 bg-purple-100 text-purple-800 rounded-full font-medium border border-purple-200">
                                            {{ getEtatLabel(piece.etat_piece) }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-2 text-sm text-gray-600">
                                        <span v-if="piece.reference_code">🏷️ Réf: {{ piece.reference_code }}</span>
                                        <span v-if="piece.emplacement">📍 {{ piece.emplacement }}</span>
                                        <span>🪛 {{ piece.vendeur }}</span>
                                    </div>
                                </div>
                                <span :class="['px-4 py-2 text-sm font-semibold rounded-lg', getPieceStatusBadge(piece).class]">
                                    {{ getPieceStatusBadge(piece).label }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <span class="text-xs text-gray-600 block mb-1">Quantité</span>
                                    <span class="font-semibold text-gray-900">{{ piece.quantite }}</span>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <span class="text-xs text-gray-600 block mb-1">Prix unitaire</span>
                                    <span class="font-semibold text-gray-900">{{ formatCurrency(piece.prix_unitaire) }}</span>
                                </div>
                                <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                                    <span class="text-xs text-indigo-600 block mb-1">Total</span>
                                    <span class="font-bold text-indigo-900">{{ formatCurrency(piece.prix_total) }}</span>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <span class="text-xs text-gray-600 block mb-1">Installation</span>
                                    <span class="font-semibold text-gray-900">{{ new Date(piece.date_installation).toLocaleDateString('fr-FR') }}</span>
                                </div>
                            </div>

                            <div class="p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-3">
                                    <div>
                                        <span class="text-xs text-gray-600 block mb-1">Limite</span>
                                        <span class="font-semibold text-gray-900">{{ piece.limite_utilisation }} {{ getUniteLabel(piece.unite_mesure) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-600 block mb-1">Utilisation</span>
                                        <span class="font-semibold text-orange-600">{{ piece.utilisation_actuelle }} {{ getUniteLabel(piece.unite_mesure) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-600 block mb-1">Restant</span>
                                        <span class="font-semibold text-green-600">{{ piece.potentiel_restant }} {{ getUniteLabel(piece.unite_mesure) }}</span>
                                    </div>
                                    <div v-if="piece.prochaine_maintenance">
                                        <span class="text-xs text-gray-600 block mb-1">Prochain</span>
                                        <span class="font-semibold text-gray-900">{{ new Date(piece.prochaine_maintenance).toLocaleDateString('fr-FR') }}</span>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div>
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>État d'usure</span>
                                        <span class="font-semibold">{{ getPourcentageRestant(piece) }}% restant</span>
                                    </div>
                                    <div class="w-full bg-gray-300 rounded-full h-3 overflow-hidden">
                                        <div
                                            :class="[
                                                'h-3 rounded-full transition-all duration-500',
                                                getPourcentageRestant(piece) > 50 ? 'bg-green-500' :
                                                getPourcentageRestant(piece) > 20 ? 'bg-yellow-500' : 'bg-red-500'
                                            ]"
                                            :style="{ width: getPourcentageRestant(piece) + '%' }"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <p v-if="piece.observation" class="mt-4 p-3 text-sm text-gray-700 italic bg-blue-50 rounded-lg border border-blue-200">
                                💬 {{ piece.observation }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Observations -->
                <div v-if="maintenance.observation_generale || maintenance.notes_validation" class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <span class="text-xl">📝</span>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Observations</h4>
                    </div>

                    <div v-if="maintenance.observation_generale" class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h5 class="text-sm font-semibold text-gray-700 mb-2">Observation générale</h5>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ maintenance.observation_generale }}</p>
                    </div>

                    <div v-if="maintenance.notes_validation" class="p-4 bg-green-50 rounded-lg border border-green-200">
                        <h5 class="text-sm font-semibold text-green-800 mb-2">✅ Notes de validation</h5>
                        <p class="text-green-700 whitespace-pre-wrap">{{ maintenance.notes_validation }}</p>
                    </div>
                </div>

                <!-- Historique -->
                <div v-if="historique && historique.length > 0" class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <span class="text-xl">📋</span>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Historique des maintenances</h4>
                    </div>
                    <div class="space-y-2">
                        <Link
                            v-for="item in historique"
                            :key="item.id"
                            :href="route('maintenances.show', item.id)"
                            class="block p-4 hover:bg-gray-50 rounded-lg transition-colors border border-gray-200 hover:border-indigo-300"
                        >
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ item.reference }}</p>
                                    <p class="text-sm text-gray-600">{{ item.nature_intervention }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ new Date(item.date_debut).toLocaleDateString('fr-FR') }}</p>
                                    <p class="text-xs text-gray-500">{{ item.kilometrage_actuel }} km</p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
