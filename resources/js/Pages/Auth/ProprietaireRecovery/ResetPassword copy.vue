<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    token: String,
    email: String,
});

const form = useForm({
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.recovery.reset-password', { token: props.token }), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Nouveau mot de passe" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-6">
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-green-800 font-medium">
                        Identité vérifiée avec succès !
                    </p>
                </div>
            </div>

            <h2 class="text-lg font-semibold text-gray-800 mb-2">
                Créer un nouveau mot de passe
            </h2>
            <p class="text-sm text-gray-600">
                Choisissez un nouveau mot de passe sécurisé pour votre compte :
                <strong>{{ email }}</strong>
            </p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Nouveau mot de passe" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
                <p class="mt-1 text-xs text-gray-500">
                    Minimum 8 caractères, incluant des lettres, chiffres et symboles.
                </p>
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmer le mot de passe" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <InputError class="mt-2" :message="form.errors.token" />

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Réinitialiser le mot de passe
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="text-sm font-semibold text-blue-800 mb-2">Conseils de sécurité :</h3>
            <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                <li>Utilisez au moins 8 caractères</li>
                <li>Mélangez majuscules, minuscules, chiffres et symboles</li>
                <li>Évitez les mots de passe courants ou évidents</li>
                <li>Ne réutilisez pas un ancien mot de passe</li>
            </ul>
        </div>
    </AuthenticationCard>
</template>
