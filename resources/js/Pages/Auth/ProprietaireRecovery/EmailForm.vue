<script setup>
import { ref, computed } from 'vue';
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

// Email validation
const isValidEmail = computed(() => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return form.email && emailRegex.test(form.email);
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

        <div class="email-form-container">
            <!-- Header Section with Icon -->
            <div class="header-section">
                <div class="icon-wrapper">
                    <svg class="lock-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>

                <h2 class="main-title">
                    Récupération de mot de passe
                </h2>

                <div class="description">
                    <p class="description-text">
                        Pour récupérer votre mot de passe, nous allons vous poser quelques questions
                        basées sur vos informations de propriétaire enregistrées dans <span class="brand-name">VEHIX</span>.
                    </p>
                    <p class="description-subtext">
                        Entrez votre adresse email pour commencer le processus.
                    </p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="recovery-form">
                <div class="form-group">
                    <InputLabel for="email" value="Adresse email" />
                    <div class="input-wrapper">
                        <div class="input-icon">
                            <svg class="envelope-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full email-input"
                            required
                            autocomplete="username"
                            placeholder="exemple@email.com"
                        />
                        <transition name="check-fade">
                            <svg v-if="isValidEmail" class="valid-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </transition>
                    </div>
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Action Buttons -->
                <div class="action-section">
                    <Link
                        :href="route('login')"
                        class="back-link"
                    >
                        <svg class="back-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Retour à la connexion</span>
                    </Link>

                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="continue-button"
                    >
                        <span>{{ form.processing ? 'Vérification...' : 'Continuer' }}</span>
                        <svg v-if="!form.processing" class="arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                        <svg v-else class="spinner-icon" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </PrimaryButton>
                </div>
            </form>

            <!-- Information Box -->
            <div class="info-box">
                <div class="info-header">
                    <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span class="info-title">Information importante</span>
                </div>
                <p class="info-text">
                    Cette méthode nécessite que vous ayez un profil de propriétaire enregistré
                    dans le système. Si vous n'avez pas de profil propriétaire, veuillez contacter
                    l'administrateur pour assistance.
                </p>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <div class="security-items">
                    <div class="security-item">
                        <svg class="security-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Connexion sécurisée</span>
                    </div>
                    <div class="security-item">
                        <svg class="security-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Données protégées</span>
                    </div>
                    <div class="security-item">
                        <svg class="security-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        <span>Email vérifié</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticationCard>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap');

.email-form-container {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    animation: fadeInScale 0.5s ease-out;
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Header Section */
.header-section {
    text-align: center;
    margin-bottom: 2.5rem;
}

.icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 4rem;
    height: 4rem;
    background: linear-gradient(135deg, #406e3f 0%, #61c97b 100%);
    border-radius: 1.25rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    animation: float 3s ease-in-out infinite;
    position: relative;
}

.icon-wrapper::before {
    content: '';
    position: absolute;
    inset: -2px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 1.25rem;
    z-index: -1;
    opacity: 0.3;
    filter: blur(10px);
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.lock-icon {
    width: 2rem;
    height: 2rem;
    color: white;
    animation: lockBounce 2s ease-in-out infinite;
}

@keyframes lockBounce {
    0%, 100% {
        transform: rotate(0deg) scale(1);
    }
    25% {
        transform: rotate(-5deg) scale(1.05);
    }
    75% {
        transform: rotate(5deg) scale(1.05);
    }
}

.main-title {
    font-family: 'Outfit', sans-serif;
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 1rem;
    letter-spacing: -0.03em;
    background: linear-gradient(135deg, #1f2937 0%, #4b5563 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.description {
    max-width: 28rem;
    margin: 0 auto;
}

.description-text {
    font-size: 0.9375rem;
    color: #4b5563;
    line-height: 1.6;
    margin-bottom: 0.75rem;
}

.brand-name {
    font-weight: 700;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.description-subtext {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

/* Form */
.recovery-form {
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    pointer-events: none;
}

.envelope-icon {
    width: 1.25rem;
    height: 1.25rem;
    color: #9ca3af;
    transition: color 0.3s ease;
}

.email-input {
    padding-left: 3rem !important;
    padding-right: 3rem !important;
    font-size: 0.9375rem;
    transition: all 0.3s ease;
}

.email-input:focus {
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.email-input:focus ~ .input-icon .envelope-icon {
    color: #667eea;
}

.valid-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    width: 1.5rem;
    height: 1.5rem;
    color: #10b981;
    animation: checkPop 0.4s ease-out;
}

.check-fade-enter-active, .check-fade-leave-active {
    transition: all 0.3s ease;
}

.check-fade-enter-from {
    opacity: 0;
    transform: translateY(-50%) scale(0.5) rotate(-180deg);
}

.check-fade-leave-to {
    opacity: 0;
    transform: translateY(-50%) scale(0.5) rotate(180deg);
}

@keyframes checkPop {
    0% {
        transform: translateY(-50%) scale(0);
        opacity: 0;
    }
    50% {
        transform: translateY(-50%) scale(1.2);
    }
    100% {
        transform: translateY(-50%) scale(1);
        opacity: 1;
    }
}

/* Action Section */
.action-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 2rem;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.3s ease;
    padding: 0.625rem 1rem;
    border-radius: 0.625rem;
}

.back-link:hover {
    color: #111827;
    background: rgba(0, 0, 0, 0.05);
    transform: translateX(-3px);
}

.back-icon {
    width: 1.125rem;
    height: 1.125rem;
    transition: transform 0.3s ease;
}

.back-link:hover .back-icon {
    transform: translateX(-2px);
}

.continue-button {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.875rem 2rem;
    font-weight: 600;
    font-size: 0.9375rem;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #406e3f 0%, #61c97b 100%);
    border: none;
    box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
}

.continue-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.continue-button:active:not(:disabled) {
    transform: translateY(0);
}

.arrow-icon {
    width: 1.125rem;
    height: 1.125rem;
    transition: transform 0.3s ease;
}

.continue-button:hover:not(:disabled) .arrow-icon {
    transform: translateX(3px);
}

.spinner-icon {
    width: 1.125rem;
    height: 1.125rem;
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

/* Info Box */
.info-box {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 2px solid #fbbf24;
    border-radius: 1rem;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    animation: slideUp 0.5s ease-out 0.2s both;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.info-header {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    margin-bottom: 0.75rem;
}

.info-icon {
    width: 1.5rem;
    height: 1.5rem;
    color: #d97706;
    flex-shrink: 0;
}

.info-title {
    font-weight: 600;
    font-size: 0.9375rem;
    color: #92400e;
}

.info-text {
    font-size: 0.875rem;
    color: #78350f;
    line-height: 1.6;
}

/* Security Notice */
.security-notice {
    animation: slideUp 0.5s ease-out 0.3s both;
}

.security-items {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.75rem;
}

.security-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    border-radius: 0.75rem;
    font-size: 0.8125rem;
    font-weight: 500;
    color: #374151;
    transition: all 0.3s ease;
}

.security-item:hover {
    background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
    transform: translateY(-2px);
}

.security-icon {
    width: 1.125rem;
    height: 1.125rem;
    color: #6b7280;
    flex-shrink: 0;
}

/* Responsive Design */
@media (max-width: 640px) {
    .main-title {
        font-size: 1.625rem;
    }

    .action-section {
        flex-direction: column-reverse;
        gap: 0.75rem;
    }

    .back-link,
    .continue-button {
        width: 100%;
        justify-content: center;
    }

    .security-items {
        grid-template-columns: 1fr;
    }
}

/* Dark mode support (optional) */
@media (prefers-color-scheme: dark) {
    .email-form-container {
        color: #e5e7eb;
    }

    .main-title {
        background: linear-gradient(135deg, #f9fafb 0%, #d1d5db 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
}
</style>
