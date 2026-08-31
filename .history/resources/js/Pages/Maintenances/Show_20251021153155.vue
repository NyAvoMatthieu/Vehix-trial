<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    maintenance: Object,
    historique: Array,
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

const getStatusBadge = (status) => {
    const badges = {
        en_attente: 'bg-yellow-100 text-yellow-800',
        en_cours: 'bg-blue-100 text-blue-800',
        validee: 'bg-green-100 text-green-800',
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        en_attente: 'En attente',
        en_cours: 'En cours',
        validee: 'Validée',
    };
    return labels[status] || status;
};

const getTypeIcon = (type) => {
    const icons = {
        preventive: '🔧',
        corrective: '⚠️',
        diagnostique: '🔍',
    };
    return icons[type] || '🔧';
};

const getTypeLabel = (type) => {
    const labels = {
        preventive: 'Préventive',
        corrective: 'Corrective',
        diagnostique: 'Diagnostique',
    };
    return labels[type] || type;
};

const getPieceStatusBadge = (piece) => {
    const pourcentage = ((piece.potentiel_restant / piece.limite_utilisation) * 100);
    
    if (pourcentage <= 0) {
        return { class: 'bg-red-100 text-red-800', label: 'Expiré' };
    } else if (pourcentage < 20) {
        return { class: 'bg-orange-100 text-orange-800', label: 'Critique' };
    } else if (pourcentage < 50) {
        return { class: 'bg-yellow-100 text-yellow-800', label: 'Attention' };
    } else {
        return { class: 'bg-green-100 text-green-800', label: 'Bon' };
    }
};

const getUniteLabel = (unite) => {
    const labels = {
        km: 'km',
        Tr: 'km',
        heures: 'h',
        cycles: 'cycles',
        jours: 'jours',
        mois: 'mois',
        annees: 'ans',
    };
    return labels[unite] || unite;
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
                        v-if="maintenance.status !== 'validee'"
                        :href="route('maintenances.edit', maintenance.id)"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                    >
                        Modifier
                    </Link>
                    <Link
                        :href="route('maintenances.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                    >
                        Retour
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-indigo-600 to-blue-800 text-white shadow-xl rounded-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-3xl">{{ getTypeIcon(maintenance.type) }}</span>
                                    <div>
                                        <h3 class="text-2xl font-bold">
                                            {{ maintenance.reference }}
                                        </h3>
                                        <p class="text-blue-100">
                                            {{ getTypeLabel(maintenance.type) }} - {{ maintenance.nature_intervention }}
                                        </p>
                                    </div>
                                </div>
                                <p class="text-blue-100 mt-2">
                                    {{ formatDate(maintenance.date_debut) }}
                                    <span v-if="maintenance.date_fin"> - {{ formatDate(maintenance.date_fin) }}</span>
                                </p>
                            </div>
                            <div class="text-right">
                                <div :class="['inline-flex px-4 py-2 rounded-full text-sm font-semibold', getStatusBadge(maintenance.status)]">
                                    {{ getStatusLabel(maintenance.status) }}
                                </div>
                                <div class="text-3xl font-bold mt-3">
                                    {{ formatCurrency(maintenance.cout_total) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle and Personnel Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white shadow-xl rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Véhicule</h4>
                        <div class="space-y-2">
                            <p class="text-lg font-semibold text-gray-900">
                                {{ maintenance.vehicule.make }} {{ maintenance.vehicule.model }}
                            </p>
                            <p class="text-sm text-gray-600">
                                Plaque: {{ maintenance.vehicule.license_plate }}
                            </p>
                            <p class="text-sm text-gray-600">
                                Kilométrage: <span class="font-medium">{{ maintenance.kilometrage_actuel }} km</span>
                            </p>
                        </div>
                    </div>

                    <div class="bg-white shadow-xl rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase mb-3">Responsables</h4>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500">Technicien</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ maintenance.technicien ? maintenance.technicien.name : maintenance.user.name }}
                                </p>
                            </div>
                            <div v-if="maintenance.validateur">
                                <p class="text-xs text-gray-500">Validé par</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ maintenance.validateur.name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Le {{ new Date(maintenance.validated_at).toLocaleString('fr-FR') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cost Breakdown -->
                <div class="bg-white shadow-xl rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Détails des coûts</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl mb-1">🔧</div>
                            <div class="text-sm text-gray-600">Main d'œuvre</div>
                            <div class="text-xl font-bold text-gray-900">
                                {{ formatCurrency(maintenance.cout_main_oeuvre) }}
                            </div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl mb-1">🔩</div>
                            <div class="text-sm text-gray-600">Pièces</div>
                            <div class="text-xl font-bold text-gray-900">
                                {{ formatCurrency(maintenance.cout_pieces) }}
                            </div>
                        </div>
                        <div class="text-center p-4 bg-indigo-50 rounded-lg border-2 border-indigo-200">
                            <div class="text-2xl mb-1">💰</div>
                            <div class="text-sm text-gray-600">Total</div>
                            <div class="text-2xl font-bold text-indigo-600">
                                {{ formatCurrency(maintenance.cout_total) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pieces -->
                <div v-if="maintenance.pieces && maintenance.pieces.length > 0" class="bg-white shadow-xl rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Pièces remplacées / inspectées</h4>
                    <div class="space-y-4">
                        <div v-for="piece in maintenance.pieces" :key="piece.id" class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h5 class="font-semibold text-gray-900">{{ piece.nom_piece }}</h5>
                                    <p v-if="piece.reference_code" class="text-sm text-gray-500">Réf: {{ piece.reference_code }}</p>
                                    <p v-if="piece.emplacement" class="text-sm text-gray-500">Emplacement: {{ piece.emplacement }}</p>
                                </div>
                                <span :class="['px-3 py-1 text-xs font-semibold rounded-full', getPieceStatusBadge(piece).class]">
                                    {{ getPieceStatusBadge(piece).label }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Quantité:</span>
                                    <span class="ml-2 font-medium">{{ piece.quantite }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Prix unitaire:</span>
                                    <span class="ml-2 font-medium">{{ formatCurrency(piece.prix_unitaire) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Total:</span>
                                    <span class="ml-2 font-medium">{{ formatCurrency(piece.prix_total) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Installation:</span>
                                    <span class="ml-2 font-medium">{{ new Date(piece.date_installation).toLocaleDateString('fr-FR') }}</span>
                                </div>
                            </div>

                            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Limite:</span>
                                        <span class="ml-2 font-medium">{{ piece.limite_utilisation }} {{ getUniteLabel(piece.unite_mesure) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Utilisation:</span>
                                        <span class="ml-2 font-medium">{{ piece.utilisation_actuelle }} {{ getUniteLabel(piece.unite_mesure) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Restant:</span>
                                        <span class="ml-2 font-medium text-green-600">{{ piece.potentiel_restant }} {{ getUniteLabel(piece.unite_mesure) }}</span>
                                    </div>
                                    <div v-if="piece.prochaine_maintenance">
                                        <span class="text-gray-600">Prochain:</span>
                                        <span class="ml-2 font-medium">{{ new Date(piece.prochaine_maintenance).toLocaleDateString('fr-FR') }}</span>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mt-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div
                                            :class="[
                                                'h-2 rounded-full transition-all',
                                                piece.potentiel_restant / piece.limite_utilisation > 0.5 ? 'bg-green-500' :
                                                piece.potentiel_restant / piece.limite_utilisation > 0.2 ? 'bg-yellow-500' : 'bg-red-500'
                                            ]"
                                            :style="{ width: ((piece.potentiel_restant / piece.limite_utilisation) * 100) + '%' }"
                                        ></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ ((piece.potentiel_restant / piece.limite_utilisation) * 100).toFixed(1) }}% restant
                                    </p>
                                </div>
                            </div>

                            <p v-if="piece.observation" class="mt-3 text-sm text-gray-600 italic">
                                📝 {{ piece.observation }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Observations -->
                <div v-if="maintenance.observation_generale || maintenance.notes_validation" class="bg-white shadow-xl rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Observations</h4>
                    
                    <div v-if="maintenance.observation_generale" class="mb-4">
                        <h5 class="text-sm font-medium text-gray-500 mb-2">Observation générale</h5>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ maintenance.observation_generale }}</p>
                    </div>

                    <div v-if="maintenance.notes_validation" class="p-4 bg-green-50 rounded-lg">
                        <h5 class="text-sm font-medium text-green-800 mb-2">Notes de validation</h5>
                        <p class="text-green-700 whitespace-pre-wrap">{{ maintenance.notes_validation }}</p>
                    </div>
                </div>

                <!-- Historique -->
                <div v-if="historique && historique.length > 0" class="bg-white shadow-xl rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Historique des maintenances</h4>
                    <div class="space-y-3">
                        <Link
                            v-for="item in historique"
                            :key="item.id"
                            :href="route('maintenances.show', item.id)"
                            class="block p-3 hover:bg-gray-50 rounded-lg transition"
                        >
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-900">{{ item.reference }}</p>
                                    <p class="text-sm text-gray-600">{{ item.nature_intervention }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-900">{{ new Date(item.date_debut).toLocaleDateString('fr-FR') }}</p>
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