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

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="h-screen flex relative overflow-hidden bg-gradient-to-br from-[#273628] via-slate-800 to-slate-900">
        <!-- Effet de fond animé -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMzLjMxIDAgNiAyLjY5IDYgNnMtMi42OSA2LTYgNi02LTIuNjktNi02IDIuNjktNiA2LTZ6TTI0IDQyYzMuMzEgMCA2IDIuNjkgNiA2cy0yLjY5IDYtNiA2LTYtMi42OS02LTYgMi42OS02IDYtNnoiIHN0cm9rZT0iIzFmMmEzZSIgc3Ryb2tlLXdpZHRoPSIuNSIgb3BhY2l0eT0iLjEiLz48L2c+PC9zdmc+')] opacity-20"></div>

        <!-- Partie gauche - Images de véhicules -->
        <div class="hidden lg:flex lg:w-1/2 relative items-center justify-center p-12" style="zoom: 0.75;">
            <div class="relative z-10 w-full max-w-2xl">
                <!-- Titre principal -->
                <div class="mb-12 text-center">
                    <h1 class="text-5xl font-bold text-white mb-4">
                        Bienvenue sur <span class="text-[#ffa500]">Vehix</span>
                    </h1>
                    <p class="text-xl text-gray-300">
                        Gérez votre flotte de véhicules en toute simplicité
                    </p>
                </div>

               <!-- Grid d'images de véhicules -->
                <div class="grid grid-cols-2 gap-6">
                    <!-- Véhicule 1 - Voitures -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <div class="aspect-[4/3] bg-gradient-to-br from-[#72c8d3] to-[#3a494f] flex flex-col items-center justify-center p-6">
                            <svg class="w-24 h-24 text-white opacity-90 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                            </svg>
                            <h3 class="text-white font-bold text-lg text-center">Voitures</h3>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <p class="text-white text-sm px-4 text-center">Gérez votre parc automobile</p>
                        </div>
                    </div>

                    <!-- Véhicule 2 - Documentation -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <div class="aspect-[4/3] bg-gradient-to-br from-[#cf98da] to-[#4e4052] flex flex-col items-center justify-center p-6">
                            <svg class="w-24 h-24 text-white opacity-90 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.5 4.5c-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5-1.45 0-2.99.22-4.28.79C1.49 5.62 1 6.33 1 7.14v11.28c0 1.3 1.22 2.26 2.48 1.94.98-.25 2.02-.36 3.02-.36 1.56 0 3.22.26 4.56.92.6.3 1.28.3 1.88 0 1.34-.67 3-.92 4.56-.92 1 0 2.04.11 3.02.36 1.26.33 2.48-.63 2.48-1.94V7.14c0-.81-.49-1.52-1.22-1.85-1.29-.57-2.83-.79-4.28-.79zM21 17.23c0 .63-.58 1.09-1.2.98-.75-.14-1.53-.2-2.3-.2-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5.92 0 1.83.09 2.7.28.46.1.8.51.8.98v9.47z"/>
                                <path d="M13.98 11.01c-.32 0-.61-.2-.71-.52-.13-.39.09-.82.48-.94 1.54-.5 3.53-.66 5.36-.45.41.05.71.42.66.83-.05.41-.42.71-.83.66-1.62-.19-3.39-.04-4.73.39-.08.01-.16.03-.23.03zM13.98 13.67c-.32 0-.61-.2-.71-.52-.13-.39.09-.82.48-.94 1.53-.5 3.53-.66 5.36-.45.41.05.71.42.66.83-.05.41-.42.71-.83.66-1.62-.19-3.39-.04-4.73.39a.991.991 0 01-.23.03zM13.98 16.33c-.32 0-.61-.2-.71-.52-.13-.39.09-.82.48-.94 1.53-.5 3.53-.66 5.36-.45.41.05.71.42.66.83-.05.41-.42.7-.83.66-1.62-.19-3.39-.04-4.73.39a.991.991 0 01-.23.03z"/>
                            </svg>
                            <h3 class="text-white font-bold text-lg text-center">Documentation</h3>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <p class="text-white text-sm px-4 text-center">Documents et historique centralisés</p>
                        </div>
                    </div>

                    <!-- Véhicule 3 - Logistique -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <div class="aspect-[4/3] bg-gradient-to-br from-[#e4de88] to-[#5e5e47] flex flex-col items-center justify-center p-6">
                            <svg class="w-24 h-24 text-white opacity-90 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                            </svg>
                            <h3 class="text-white font-bold text-lg text-center">Logistique</h3>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <p class="text-white text-sm px-4 text-center">Planification et coordination des flux</p>
                        </div>
                    </div>

                    <!-- Véhicule 4 - Maintenance -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <div class="aspect-[4/3] bg-gradient-to-br from-[#7af77e] to-[#4d6a4e] flex flex-col items-center justify-center p-6">
                            <svg class="w-24 h-24 text-white opacity-90 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm6 9.09c0 4-2.55 7.7-6 8.83-3.45-1.13-6-4.82-6-8.83V6.31l6-2.12 6 2.12v4.78z"/>
                                <path d="M10.23 14.83L7.4 12l-1.41 1.41L10.23 18 18 10.23 16.59 8.82z"/>
                            </svg>
                            <h3 class="text-white font-bold text-lg text-center">Maintenance</h3>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <p class="text-white text-sm px-4 text-center">Planification des entretiens</p>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="mt-12 grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white mb-1">232+</div>
                        <div class="text-sm text-gray-400">Véhicules</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white mb-1">95%</div>
                        <div class="text-sm text-gray-400">Satisfaction</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white mb-1">24/7</div>
                        <div class="text-sm text-gray-400">Support</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Partie droite - Formulaire de connexion -->
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12" style="zoom: 0.75;">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <div class="bg-white/10 backdrop-blur-lg p-4 rounded-2xl shadow-2xl">
                        <AuthenticationCardLogo />
                    </div>
                </div>

                <!-- Carte de login -->
                <div class="bg-white/95 backdrop-blur-xl shadow-2xl rounded-3xl p-8 border border-gray-100">
                    <div class="mb-8 text-center">
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">Connexion</h2>
                        <p class="text-gray-600">Accédez à votre espace de gestion</p>
                    </div>

                    <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <p class="text-sm text-green-700 font-medium">{{ status }}</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="email" value="Email" class="text-gray-700 font-semibold mb-2" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full px-4 py-3 rounded-xl border-gray-300 focus:border-[#273628] focus:ring-[#273628] transition-colors"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder=""
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div>
                            <InputLabel for="password" value="Mot de passe" class="text-gray-700 font-semibold mb-2" />
                            <div class="relative">
                                <TextInput
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="mt-1 block w-full px-4 py-3 pr-12 rounded-xl border-gray-300 focus:border-[#273628] focus:ring-[#273628] transition-colors"
                                    required
                                    autocomplete="current-password"
                                    placeholder=""
                                />
                                <button
                                    type="button"
                                    @click="togglePasswordVisibility"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 transition-colors"
                                    tabindex="-1"
                                >
                                    <!-- Icône œil ouvert (password visible) -->
                                    <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <!-- Icône œil barré (password masqué) -->
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer group">
                                <Checkbox v-model:checked="form.remember" name="remember" class="rounded border-gray-300 text-[#273628] focus:ring-[#273628]" />
                                <span class="ms-2 text-sm text-gray-600 group-hover:text-gray-800 transition-colors">Se souvenir de moi</span>
                            </label>

                            <Link
                                :href="route('password.recovery.email')"
                                class="text-sm text-[#273628] hover:text-[#3a4f3b] font-medium transition-colors"
                            >
                                Mot de passe oublié?
                            </Link>

                            <!--<Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-sm text-[#273628] hover:text-[#3a4f3b] font-medium transition-colors"
                            >
                                Mot de passe oublié?
                            </Link>
                            <Link
                                :href="route('password.recovery.email')"
                                class="underline text-sm text-blue-600 hover:text-blue-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Récupération via données propriétaire
                            </Link>-->
                        </div>

                        <PrimaryButton
                            class="w-full justify-center py-3 px-6 bg-gradient-to-r from-[#273628] to-[#3a4f3b] hover:from-[#2d3e2f] hover:to-[#405241] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-if="!form.processing">Se connecter</span>
                            <span v-else>Connexion en cours...</span>
                        </PrimaryButton>
                    </form>

                    <!-- Information supplémentaire -->
                    <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-xs text-gray-600">
                            <strong>Note :</strong> Si vous avez enregistré un profil de propriétaire dans VEHIX,
                            vous pouvez utiliser la récupération via données propriétaire pour réinitialiser
                            votre mot de passe sans email.
                        </p>
                    </div>

                    <!-- Divider -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-center">
                            <p class="text-sm text-gray-600 flex items-center">
                                Propulsé par
                                <Link :href="'/'" class="inline-flex items-center hover:opacity-80 transition-opacity ml-1">
                                    <img
                                        src="hasnreziga.png"
                                        alt="hasnreziga"
                                        class="w-10 h-10"
                                    />
                                    <span class="ml-1 font-semibold text-gray-800">Hasnreziga</span>
                                </Link>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Message mobile -->
                <div class="lg:hidden mt-6 text-center">
                    <p class="text-sm text-gray-300">
                        Gérez votre flotte de véhicules en toute simplicité
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
