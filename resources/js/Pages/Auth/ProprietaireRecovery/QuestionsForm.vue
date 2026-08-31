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

// Track answered questions
const answeredQuestions = computed(() => {
    return Object.values(form.answers).filter(answer => answer && answer.trim() !== '').length;
});

const totalQuestions = computed(() => props.questions.length);

const progressPercentage = computed(() => {
    return (answeredQuestions.value / totalQuestions.value) * 100;
});

const submit = () => {
    form.post(route('password.recovery.verify-answers', { token: props.token }));
};

const getInputType = (type) => {
    return type === 'date' ? 'date' : 'text';
};

const getQuestionIcon = (index) => {
    const icons = [
        'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', // User
        'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', // Home
        'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', // Calendar
        'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', // Document
        'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', // Building
    ];
    return icons[index % icons.length];
};
</script>

<template>
    <Head title="Questions de vérification" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="questions-form-container">
            <!-- Two Column Layout -->
            <div class="two-column-layout">
                <!-- Left Column: Header and Progress -->
                <div class="left-column">
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="verification-badge">
                            <svg class="shield-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        <h2 class="main-title">
                            Vérification d'identité
                        </h2>

                        <p class="subtitle">
                            Répondez aux questions basées sur vos informations de propriétaire
                            <span class="owner-type">{{ proprietaireType === 'personnel' ? 'personnel' : 'd\'entreprise' }}</span>.
                        </p>

                        <!-- Progress Bar -->
                        <div class="progress-section">
                            <div class="progress-header">
                                <span class="progress-label">Progression</span>
                                <span class="progress-count">{{ answeredQuestions }} / {{ totalQuestions }}</span>
                            </div>
                            <div class="progress-bar-container">
                                <div
                                    class="progress-bar-fill"
                                    :style="{ width: `${progressPercentage}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Warning Box -->
                    <div class="warning-box">
                        <svg class="warning-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="warning-content">
                            <p class="warning-title">Important</p>
                            <p class="warning-text">
                                Vous avez <strong>3 tentatives</strong>. Après 3 échecs, vous serez bloqué pendant <strong>30 minutes</strong>.
                            </p>
                        </div>
                    </div>

                    <!-- Help Section -->
                    <div class="help-section">
                        <svg class="help-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                        <p class="help-text">
                            Besoin d'aide ? Si vous ne vous souvenez pas de vos informations, contactez l'administrateur.
                        </p>
                    </div>
                </div>

                <!-- Right Column: Form with Questions -->
                <div class="right-column">
                    <form @submit.prevent="submit" class="verification-form">
                        <div class="questions-list">
                            <div
                                v-for="(question, index) in questions"
                                :key="question.id"
                                class="question-card"
                                :class="{ 'answered': form.answers[question.id] && form.answers[question.id].trim() !== '' }"
                            >
                                <div class="question-header">
                                    <div class="question-number">
                                        <svg class="question-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getQuestionIcon(index)"/>
                                        </svg>
                                        <span class="number-badge">{{ index + 1 }}</span>
                                    </div>
                                    <div class="question-status">
                                        <transition name="check-fade">
                                            <svg v-if="form.answers[question.id] && form.answers[question.id].trim() !== ''" class="check-icon" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </transition>
                                    </div>
                                </div>

                                <div class="question-content">
                                    <InputLabel
                                        :for="`question-${question.id}`"
                                        :value="question.question"
                                        class="question-label"
                                    />
                                    <div class="input-wrapper">
                                        <TextInput
                                            :id="`question-${question.id}`"
                                            v-model="form.answers[question.id]"
                                            :type="getInputType(question.type)"
                                            class="mt-2 block w-full question-input"
                                            :required="question.required"
                                            :placeholder="question.type === 'date' ? 'AAAA-MM-JJ' : 'Entrez votre réponse...'"
                                        />
                                        <div v-if="question.type === 'date'" class="input-hint">
                                            <svg class="hint-icon" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Format : AAAA-MM-JJ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <InputError class="mt-4" :message="form.errors.answers" />
                        <InputError class="mt-2" :message="form.errors.token" />

                        <!-- Action Section -->
                        <div class="action-section">
                            <Link
                                :href="route('password.recovery.email')"
                                class="restart-link"
                            >
                                <svg class="restart-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span>Recommencer</span>
                            </Link>

                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                class="verify-button"
                            >
                                <span>{{ form.processing ? 'Vérification...' : 'Vérifier' }}</span>
                                <svg v-if="!form.processing" class="arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <svg v-else class="spinner-icon" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticationCard>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

.questions-form-container {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    animation: fadeIn 0.6s ease-out;
}

/* Two Column Layout */
.two-column-layout {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 2.5rem;
    align-items: start;
    max-width: 1400px;
    margin: 0 auto;
}

.left-column {
    position: sticky;
    top: 1rem;
}

.right-column {
    min-height: 100%;
    max-width: 900px;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Header Section */
.header-section {
    text-align: center;
    margin-bottom: 1.5rem;
}

.verification-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 4rem;
    height: 4rem;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 1.25rem;
    margin-bottom: 1rem;
    box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
    animation: pulse 2s ease-in-out infinite;
    position: relative;
}

.verification-badge::before {
    content: '';
    position: absolute;
    inset: -3px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 1.25rem;
    z-index: -1;
    opacity: 0.4;
    filter: blur(15px);
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 15px 50px rgba(16, 185, 129, 0.4);
    }
}

.shield-icon {
    width: 2rem;
    height: 2rem;
    color: white;
}

.main-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.625rem;
    letter-spacing: -0.03em;
    background: linear-gradient(135deg, #1f2937 0%, #4b5563 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.subtitle {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.5;
    margin: 0 auto 1rem;
}

.owner-type {
    font-weight: 600;
    color: #10b981;
}

/* Progress Section */
.progress-section {
    margin-top: 1rem;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.progress-label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: #374151;
}

.progress-count {
    font-size: 0.8125rem;
    font-weight: 700;
    color: #10b981;
}

.progress-bar-container {
    height: 0.625rem;
    background: #e5e7eb;
    border-radius: 9999px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
    border-radius: 9999px;
    transition: width 0.5s ease;
    position: relative;
    overflow: hidden;
}

.progress-bar-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

/* Warning Box */
.warning-box {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 2px solid #f59e0b;
    border-radius: 0.875rem;
    margin-bottom: 1.25rem;
    animation: slideDown 0.5s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.warning-icon {
    width: 1.25rem;
    height: 1.25rem;
    color: #d97706;
    flex-shrink: 0;
    animation: wiggle 1s ease-in-out infinite;
}

@keyframes wiggle {
    0%, 100% {
        transform: rotate(0deg);
    }
    25% {
        transform: rotate(-5deg);
    }
    75% {
        transform: rotate(5deg);
    }
}

.warning-content {
    flex: 1;
}

.warning-title {
    font-weight: 700;
    font-size: 0.875rem;
    color: #92400e;
    margin-bottom: 0.25rem;
}

.warning-text {
    font-size: 0.8125rem;
    color: #78350f;
    line-height: 1.5;
}

/* Questions List */
.verification-form {
    margin-bottom: 0;
}

.questions-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.question-card {
    background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
    border: 2px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1.25rem;
    transition: all 0.3s ease;
    animation: slideIn 0.4s ease-out both;
}

.question-card:nth-child(1) { animation-delay: 0.1s; }
.question-card:nth-child(2) { animation-delay: 0.2s; }
.question-card:nth-child(3) { animation-delay: 0.3s; }
.question-card:nth-child(4) { animation-delay: 0.4s; }
.question-card:nth-child(5) { animation-delay: 0.5s; }

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.question-card:hover {
    border-color: #10b981;
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.15);
    transform: translateY(-2px);
}

.question-card.answered {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-color: #10b981;
}

.question-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.875rem;
}

.question-number {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.question-icon {
    width: 2.25rem;
    height: 2.25rem;
    padding: 0.5rem;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border-radius: 0.625rem;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}

.number-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.875rem;
    height: 1.875rem;
    background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
    color: white;
    font-weight: 700;
    font-size: 0.875rem;
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.question-status {
    width: 1.875rem;
    height: 1.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.check-icon {
    width: 1.875rem;
    height: 1.875rem;
    color: #10b981;
}

.check-fade-enter-active, .check-fade-leave-active {
    transition: all 0.4s ease;
}

.check-fade-enter-from {
    opacity: 0;
    transform: scale(0) rotate(-180deg);
}

.check-fade-leave-to {
    opacity: 0;
    transform: scale(0) rotate(180deg);
}

.question-content {
    margin-left: 3rem;
}

.question-label {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.5;
}

.input-wrapper {
    position: relative;
}

.question-input {
    font-size: 0.9375rem;
    transition: all 0.3s ease;
}

.question-input:focus {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    border-color: #10b981;
}

.input-hint {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    margin-top: 0.5rem;
    font-size: 0.8125rem;
    color: #6b7280;
}

.hint-icon {
    width: 1rem;
    height: 1rem;
    color: #9ca3af;
}

/* Action Section */
.action-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 1.25rem;
    padding-top: 1.25rem;
    border-top: 2px solid #e5e7eb;
}

.restart-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-decoration: none;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}

.restart-link:hover {
    color: #111827;
    background: rgba(0, 0, 0, 0.05);
}

.restart-icon {
    width: 1.25rem;
    height: 1.25rem;
    transition: transform 0.3s ease;
}

.restart-link:hover .restart-icon {
    transform: rotate(-45deg);
}

.verify-button {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.875rem 1.75rem;
    font-weight: 600;
    font-size: 0.9375rem;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
    transition: all 0.3s ease;
}

.verify-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
}

.verify-button:active:not(:disabled) {
    transform: translateY(0);
}

.arrow-icon {
    width: 1.25rem;
    height: 1.25rem;
}

.spinner-icon {
    width: 1.25rem;
    height: 1.25rem;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Help Section */
.help-section {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.875rem 1.125rem;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    border-radius: 0.875rem;
    animation: fadeIn 0.6s ease-out 0.6s both;
}

.help-icon {
    width: 1.125rem;
    height: 1.125rem;
    color: #6b7280;
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.help-text {
    font-size: 0.8125rem;
    color: #4b5563;
    line-height: 1.5;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .two-column-layout {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .left-column {
        position: relative;
    }
}

@media (max-width: 640px) {
    .main-title {
        font-size: 1.625rem;
    }

    .action-section {
        flex-direction: column-reverse;
        gap: 0.75rem;
    }

    .restart-link,
    .verify-button {
        width: 100%;
        justify-content: center;
    }

    .question-content {
        margin-left: 0;
        margin-top: 0.75rem;
    }

    .question-number {
        flex-wrap: wrap;
    }
}
</style>
