<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmationVisibility = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <div class="h-screen flex relative overflow-hidden bg-gradient-to-br from-[#273628] via-slate-800 to-slate-900">
        <!-- Effet de fond animé -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMzLjMxIDAgNiAyLjY5IDYgNnMtMi42OSA2LTYgNi02LTIuNjktNi02IDIuNjktNiA2LTZ6TTI0IDQyYzMuMzEgMCA2IDIuNjkgNiA2cy0yLjY5IDYtNiA2LTYtMi42OS02LTYgMi42OS02IDYtNnoiIHN0cm9rZT0iIzFmMmEzZSIgc3Ryb2tlLXdpZHRoPSIuNSIgb3BhY2l0eT0iLjEiLz48L2c+PC9zdmc+')] opacity-20"></div>

        <!-- Partie gauche - Avantages de l'inscription -->
        <div class="hidden lg:flex lg:w-1/2 relative items-center justify-center p-12" style="zoom: 0.75;">
            <div class="relative z-10 w-full max-w-2xl">
                <!-- Titre principal -->
                <div class="mb-12 text-center">
                    <h1 class="text-5xl font-bold text-white mb-4">
                        Rejoignez <span class="text-[#ffa500]">Vehix</span>
                    </h1>
                    <p class="text-xl text-gray-300">
                        Créez votre compte et profitez de tous nos avantages
                    </p>
                </div>

                <!-- Grid d'avantages -->
                <div class="grid grid-cols-2 gap-6">
                    <!-- Avantage 1 - Gestion Facile -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <div class="aspect-[4/3] bg-gradient-to-br from-[#7af77e] to-[#4d6a4e] flex flex-col items-center justify-center p-6">
                            <svg class="w-24 h-24 text-white opacity-90 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                            </svg>
                            <h3 class="text-white font-bold text-lg text-center">Gestion Simplifiée</h3>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <p class="text-white text-sm px-4 text-center">Gérez tous vos véhicules en un seul endroit</p>
                        </div>
                    </div>

                    <!-- Avantage 2 - Sécurité -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <div class="aspect-[4/3] bg-gradient-to-br from-[#72c8d3] to-[#3a494f] flex flex-col items-center justify-center p-6">
                            <svg class="w-24 h-24 text-white opacity-90 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                            </svg>
                            <h3 class="text-white font-bold text-lg text-center">Sécurité Maximale</h3>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <p class="text-white text-sm px-4 text-center">Vos données sont protégées</p>
                        </div>
                    </div>

                    <!-- Avantage 3 - Suivi en temps réel -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <div class="aspect-[4/3] bg-gradient-to-br from-[#e4de88] to-[#5e5e47] flex flex-col items-center justify-center p-6">
                            <svg class="w-24 h-24 text-white opacity-90 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                            </svg>
                            <h3 class="text-white font-bold text-lg text-center">Suivi en Temps Réel</h3>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <p class="text-white text-sm px-4 text-center">Suivez vos véhicules en direct</p>
                        </div>
                    </div>

                    <!-- Avantage 4 - Support 24/7 -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <div class="aspect-[4/3] bg-gradient-to-br from-[#cf98da] to-[#4e4052] flex flex-col items-center justify-center p-6">
                            <svg class="w-24 h-24 text-white opacity-90 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                            </svg>
                            <h3 class="text-white font-bold text-lg text-center">Support 24/7</h3>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <p class="text-white text-sm px-4 text-center">Assistance disponible à tout moment</p>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="mt-12 grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white mb-1">5 min</div>
                        <div class="text-sm text-gray-400">Inscription rapide</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white mb-1">Gratuit</div>
                        <div class="text-sm text-gray-400">Disponible chaque jour</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white mb-1">100+</div>
                        <div class="text-sm text-gray-400">Utilisateurs actifs</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Partie droite - Formulaire d'inscription -->
        <div class="flex-1 flex items-center justify-center p-4 lg:p-8 overflow-y-auto" style="zoom: 0.85;">
            <div class="w-full max-w-md py-6">
                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <div class="bg-white/10 backdrop-blur-lg p-3 rounded-2xl shadow-2xl">
                        <AuthenticationCardLogo />
                    </div>
                </div>

                <!-- Carte d'inscription -->
                <div class="bg-white/95 backdrop-blur-xl shadow-2xl rounded-3xl p-6 lg:p-8 border border-gray-100 max-h-[calc(100vh-8rem)] overflow-y-auto">
                    <div class="mb-6 text-center">
                        <h2 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-2">Créer un compte</h2>
                        <p class="text-sm lg:text-base text-gray-600">Rejoignez notre plateforme de gestion</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="name" value="Nom et Prénom" class="text-gray-700 font-semibold mb-1.5 text-sm" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full px-3 py-2.5 text-sm rounded-xl border-gray-300 focus:border-[#273628] focus:ring-[#273628] transition-colors"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder=""
                            />
                            <InputError class="mt-1.5" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email" class="text-gray-700 font-semibold mb-1.5 text-sm" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full px-3 py-2.5 text-sm rounded-xl border-gray-300 focus:border-[#273628] focus:ring-[#273628] transition-colors"
                                required
                                autocomplete="username"
                                placeholder=""
                            />
                            <InputError class="mt-1.5" :message="form.errors.email" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="password" value="Mot de passe" class="text-gray-700 font-semibold mb-1.5 text-sm" />
                                <div class="relative">
                                    <TextInput
                                        id="password"
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        class="mt-1 block w-full px-3 py-2.5 pr-10 text-sm rounded-xl border-gray-300 focus:border-[#273628] focus:ring-[#273628] transition-colors"
                                        required
                                        autocomplete="new-password"
                                        placeholder=""
                                    />
                                    <button
                                        type="button"
                                        @click="togglePasswordVisibility"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 transition-colors"
                                        tabindex="-1"
                                    >
                                        <!-- Icône œil ouvert -->
                                        <svg v-if="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <!-- Icône œil barré -->
                                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                <InputError class="mt-1.5" :message="form.errors.password" />
                            </div>

                            <div>
                                <InputLabel for="password_confirmation" value="Confirmer" class="text-gray-700 font-semibold mb-1.5 text-sm" />
                                <div class="relative">
                                    <TextInput
                                        id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        :type="showPasswordConfirmation ? 'text' : 'password'"
                                        class="mt-1 block w-full px-3 py-2.5 pr-10 text-sm rounded-xl border-gray-300 focus:border-[#273628] focus:ring-[#273628] transition-colors"
                                        required
                                        autocomplete="new-password"
                                        placeholder=""
                                    />
                                    <button
                                        type="button"
                                        @click="togglePasswordConfirmationVisibility"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 transition-colors"
                                        tabindex="-1"
                                    >
                                        <!-- Icône œil ouvert -->
                                        <svg v-if="showPasswordConfirmation" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <!-- Icône œil barré -->
                                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                <InputError class="mt-1.5" :message="form.errors.password_confirmation" />
                            </div>
                        </div>

                        <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="pt-2">
                            <label for="terms" class="flex items-start cursor-pointer group">
                                <Checkbox
                                    id="terms"
                                    v-model:checked="form.terms"
                                    name="terms"
                                    required
                                    class="mt-0.5 rounded border-gray-300 text-[#273628] focus:ring-[#273628] flex-shrink-0"
                                />
                                <div class="ms-2.5 text-xs lg:text-sm text-gray-600 group-hover:text-gray-800 transition-colors leading-relaxed">
                                    J'accepte les
                                    <a
                                        target="_blank"
                                        :href="route('terms.show')"
                                        class="text-[#273628] hover:text-[#3a4f3b] font-medium underline"
                                    >
                                        Conditions d'utilisation
                                    </a>
                                    et la
                                    <a
                                        target="_blank"
                                        :href="route('policy.show')"
                                        class="text-[#273628] hover:text-[#3a4f3b] font-medium underline"
                                    >
                                        Politique de confidentialité
                                    </a>
                                </div>
                            </label>
                            <InputError class="mt-1.5 ms-6" :message="form.errors.terms" />
                        </div>

                        <div class="pt-2">
                            <PrimaryButton
                                class="w-full justify-center py-2.5 lg:py-3 px-6 bg-gradient-to-r from-[#273628] to-[#3a4f3b] hover:from-[#2d3e2f] hover:to-[#405241] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 text-sm lg:text-base"
                                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                :disabled="form.processing"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2.5 h-4 w-4 lg:h-5 lg:w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span v-if="!form.processing">S'inscrire</span>
                                <span v-else>Création en cours...</span>
                            </PrimaryButton>
                        </div>

                        <div class="pt-3 text-center">
                            <p class="text-xs lg:text-sm text-gray-600">
                                Déjà inscrit?
                                <Link
                                    :href="route('login')"
                                    class="text-[#273628] hover:text-[#3a4f3b] font-semibold transition-colors ml-1"
                                >
                                    Se connecter
                                </Link>
                            </p>
                        </div>
                    </form>

                    <!-- Divider -->
                    <div class="mt-6 pt-5 border-t border-gray-200">
                        <div class="flex justify-center">
                            <p class="text-xs lg:text-sm text-gray-600 flex items-center">
                                Propulsé par
                                <Link :href="'/'" class="inline-flex items-center hover:opacity-80 transition-opacity ml-1">
                                    <img
                                        src="hasnreziga.png"
                                        alt="hasnreziga"
                                        class="w-8 h-8 lg:w-10 lg:h-10"
                                    />
                                    <span class="ml-1 font-semibold text-gray-800">Hasnreziga</span>
                                </Link>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Message mobile -->
                <div class="lg:hidden mt-4 text-center">
                    <p class="text-xs text-gray-300">
                        Créez votre compte et commencez à gérer votre flotte
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Animations personnalisées */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.group:hover svg {
    animation: float 2s ease-in-out infinite;
}

/* Scrollbar personnalisée pour la carte */
.max-h-\[calc\(100vh-8rem\)\]::-webkit-scrollbar {
    width: 4px;
}

.max-h-\[calc\(100vh-8rem\)\]::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 10px;
}

.max-h-\[calc\(100vh-8rem\)\]::-webkit-scrollbar-thumb {
    background: rgba(39, 54, 40, 0.3);
    border-radius: 10px;
}

.max-h-\[calc\(100vh-8rem\)\]::-webkit-scrollbar-thumb:hover {
    background: rgba(39, 54, 40, 0.5);
}

/* Scrollbar pour la partie droite (mobile) */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Désactiver l'icône native de Chrome pour les champs password */
input[type="password"]::-ms-reveal,
input[type="password"]::-ms-clear {
    display: none;
}

input[type="password"]::-webkit-credentials-auto-fill-button,
input[type="password"]::-webkit-contacts-auto-fill-button {
    visibility: hidden;
    pointer-events: none;
    position: absolute;
    right: 0;
}

/* Chrome, Safari, Edge */
input[type="password"]::-webkit-textfield-decoration-container {
    visibility: hidden;
    pointer-events: none;
}

input[type="text"]::-ms-reveal,
input[type="text"]::-ms-clear {
    display: none;
}
</style>
