<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.recovery.verify-email'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Récupération de mot de passe" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-4 text-sm text-gray-600">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">
                Récupération de mot de passe
            </h2>
            <p>
                Pour récupérer votre mot de passe, nous allons vous poser quelques questions
                basées sur vos informations de propriétaire enregistrées dans VEHIX.
            </p>
            <p class="mt-2">
                Entrez votre adresse email pour commencer.
            </p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <Link
                    :href="route('login')"
                    class="text-sm text-gray-600 hover:text-gray-900 underline"
                >
                    Retour à la connexion
                </Link>

                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Continuer
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                <strong>Note :</strong> Cette méthode nécessite que vous ayez un profil
                de propriétaire enregistré dans le système. Si vous n'avez pas de profil
                propriétaire, veuillez contacter l'administrateur.
            </p>
        </div>
    </AuthenticationCard>
</template>
