<script setup>
import { ref, computed } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    token: String,
    questions: Array,
    proprietaireType: String,
});

const form = useForm({
    answers: {},
});

// Initialiser les réponses
props.questions.forEach(question => {
    form.answers[question.id] = '';
});

const submit = () => {
    form.post(route('password.recovery.verify-answers', { token: props.token }));
};

const getInputType = (type) => {
    return type === 'date' ? 'date' : 'text';
};
</script>

<template>
    <Head title="Questions de vérification" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">
                Vérification d'identité
            </h2>
            <p class="text-sm text-gray-600">
                Veuillez répondre aux questions suivantes basées sur vos informations
                de propriétaire {{ proprietaireType === 'personnel' ? 'personnel' : 'd\'entreprise' }}.
            </p>
            <p class="text-sm text-amber-600 mt-2">
                <strong>Important :</strong> Vous avez 3 tentatives. Après 3 échecs,
                vous serez bloqué pendant 30 minutes.
            </p>
        </div>

        <form @submit.prevent="submit">
            <div class="space-y-4">
                <div v-for="(question, index) in questions" :key="question.id">
                    <InputLabel
                        :for="`question-${question.id}`"
                        :value="`${index + 1}. ${question.question}`"
                        class="font-medium"
                    />
                    <TextInput
                        :id="`question-${question.id}`"
                        v-model="form.answers[question.id]"
                        :type="getInputType(question.type)"
                        class="mt-2 block w-full"
                        :required="question.required"
                        :placeholder="question.type === 'date' ? 'AAAA-MM-JJ' : 'Votre réponse'"
                    />
                </div>

                <InputError class="mt-2" :message="form.errors.answers" />
                <InputError class="mt-2" :message="form.errors.token" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <Link
                    :href="route('password.recovery.email')"
                    class="text-sm text-gray-600 hover:text-gray-900 underline"
                >
                    Recommencer
                </Link>

                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Vérifier
                </PrimaryButton>
            </div>
        </form>

    </AuthenticationCard>
</template>
