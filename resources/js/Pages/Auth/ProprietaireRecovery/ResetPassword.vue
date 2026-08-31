<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    token: String,
    email: String,
});

const form = useForm({
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

// Password strength indicator
const passwordStrength = computed(() => {
    const pwd = form.password;
    if (!pwd) return { level: 0, text: '', color: '' };

    let strength = 0;
    if (pwd.length >= 8) strength++;
    if (pwd.length >= 12) strength++;
    if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) strength++;
    if (/\d/.test(pwd)) strength++;
    if (/[^A-Za-z0-9]/.test(pwd)) strength++;

    const levels = [
        { level: 0, text: '', color: '' },
        { level: 1, text: 'Faible', color: 'bg-red-500' },
        { level: 2, text: 'Moyen', color: 'bg-orange-500' },
        { level: 3, text: 'Bon', color: 'bg-yellow-500' },
        { level: 4, text: 'Fort', color: 'bg-lime-500' },
        { level: 5, text: 'Excellent', color: 'bg-emerald-500' },
    ];

    return levels[strength];
});

const passwordsMatch = computed(() => {
    return form.password && form.password_confirmation &&
           form.password === form.password_confirmation;
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

        <div class="reset-password-container">
            <!-- Success Banner with Animation -->
            <div class="success-banner">
                <div class="success-icon-wrapper">
                    <svg class="success-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="success-content">
                    <p class="success-title">Identité vérifiée</p>
                    <p class="success-subtitle">Votre identité a été confirmée avec succès</p>
                </div>
            </div>

            <!-- Header Section -->
            <div class="header-section">
                <h2 class="main-title">
                    Créer un nouveau mot de passe
                </h2>
                <p class="subtitle">
                    Définissez un mot de passe sécurisé pour
                    <span class="email-highlight">{{ email }}</span>
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="password-form">
                <!-- New Password Field -->
                <div class="form-group">
                    <InputLabel for="password" value="Nouveau mot de passe" />
                    <div class="input-wrapper">
                        <TextInput
                            id="password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            class="mt-1 block w-full password-input"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="toggle-password"
                            tabindex="-1"
                        >
                            <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Password Strength Indicator -->
                    <div v-if="form.password" class="password-strength">
                        <div class="strength-bars">
                            <div
                                v-for="i in 5"
                                :key="i"
                                class="strength-bar"
                                :class="[
                                    i <= passwordStrength.level ? passwordStrength.color : 'bg-gray-200',
                                    { 'active': i <= passwordStrength.level }
                                ]"
                            ></div>
                        </div>
                        <p v-if="passwordStrength.text" class="strength-text">
                            Force : <span :class="passwordStrength.color.replace('bg-', 'text-')">{{ passwordStrength.text }}</span>
                        </p>
                    </div>

                    <InputError class="mt-2" :message="form.errors.password" />
                    <p class="field-hint">
                        <svg class="hint-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Minimum 8 caractères avec lettres, chiffres et symboles
                    </p>
                </div>

                <!-- Confirm Password Field -->
                <div class="form-group">
                    <InputLabel for="password_confirmation" value="Confirmer le mot de passe" />
                    <div class="input-wrapper">
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            :type="showPasswordConfirmation ? 'text' : 'password'"
                            class="mt-1 block w-full password-input"
                            :class="{ 'border-emerald-400': passwordsMatch }"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                        />
                        <button
                            type="button"
                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                            class="toggle-password"
                            tabindex="-1"
                        >
                            <svg v-if="!showPasswordConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                        <transition name="check-fade">
                            <svg v-if="passwordsMatch" class="match-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </transition>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <InputError class="mt-2" :message="form.errors.token" />

                <!-- Submit Button -->
                <div class="submit-section">
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="submit-button"
                    >
                        <svg v-if="!form.processing" class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        <svg v-else class="button-icon animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ form.processing ? 'Réinitialisation...' : 'Réinitialiser le mot de passe' }}</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticationCard>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=DM+Sans:wght@400;500;600&display=swap');

.reset-password-container {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Success Banner */
.success-banner {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
    border-radius: 1rem;
    margin-bottom: 2rem;
    border: 2px solid rgba(16, 185, 129, 0.2);
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.15);
    animation: slideInDown 0.5s ease-out, pulse 2s ease-in-out infinite;
    position: relative;
    overflow: hidden;
}

.success-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
    animation: shimmer 3s linear infinite;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 4px 20px rgba(16, 185, 129, 0.15);
    }
    50% {
        box-shadow: 0 4px 30px rgba(16, 185, 129, 0.25);
    }
}

@keyframes shimmer {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.success-icon-wrapper {
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}

.success-icon {
    width: 1.75rem;
    height: 1.75rem;
    color: #059669;
    animation: checkmark 0.6s ease-out 0.3s both;
}

@keyframes checkmark {
    0% {
        transform: scale(0) rotate(-45deg);
    }
    50% {
        transform: scale(1.2) rotate(10deg);
    }
    100% {
        transform: scale(1) rotate(0deg);
    }
}

.success-content {
    flex: 1;
    position: relative;
    z-index: 1;
}

.success-title {
    font-family: 'Outfit', sans-serif;
    font-weight: 600;
    font-size: 1rem;
    color: #065f46;
    margin-bottom: 0.25rem;
}

.success-subtitle {
    font-size: 0.875rem;
    color: #047857;
    opacity: 0.9;
}

/* Header Section */
.header-section {
    margin-bottom: 2rem;
}

.main-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.75rem;
    letter-spacing: -0.025em;
    background: linear-gradient(135deg, #111827 0%, #374151 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.subtitle {
    font-size: 0.9375rem;
    color: #6b7280;
    line-height: 1.6;
}

.email-highlight {
    font-weight: 600;
    color: #2563eb;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Form */
.password-form {
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

.password-input {
    padding-right: 3rem !important;
    font-family: 'DM Sans', sans-serif;
    transition: all 0.3s ease;
}

.password-input:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.toggle-password {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    padding: 0.5rem;
    color: #9ca3af;
    transition: all 0.2s ease;
    border-radius: 0.5rem;
    background: transparent;
    border: none;
    cursor: pointer;
}

.toggle-password:hover {
    color: #6b7280;
    background: rgba(0, 0, 0, 0.05);
}

.toggle-password:active {
    transform: translateY(-50%) scale(0.95);
}

.match-icon {
    position: absolute;
    right: 3rem;
    top: 50%;
    transform: translateY(-50%);
    width: 1.25rem;
    height: 1.25rem;
    color: #10b981;
    animation: checkmark 0.4s ease-out;
}

.check-fade-enter-active, .check-fade-leave-active {
    transition: all 0.3s ease;
}

.check-fade-enter-from {
    opacity: 0;
    transform: translateY(-50%) scale(0.5);
}

.check-fade-leave-to {
    opacity: 0;
    transform: translateY(-50%) scale(0.5);
}

/* Password Strength */
.password-strength {
    margin-top: 0.75rem;
}

.strength-bars {
    display: flex;
    gap: 0.375rem;
    margin-bottom: 0.5rem;
}

.strength-bar {
    height: 0.375rem;
    flex: 1;
    border-radius: 9999px;
    transition: all 0.3s ease;
}

.strength-bar.active {
    animation: fillBar 0.3s ease-out;
}

@keyframes fillBar {
    from {
        transform: scaleX(0);
        opacity: 0;
    }
    to {
        transform: scaleX(1);
        opacity: 1;
    }
}

.strength-text {
    font-size: 0.8125rem;
    color: #6b7280;
    font-weight: 500;
}

.field-hint {
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
    flex-shrink: 0;
    color: #9ca3af;
}

/* Submit Section */
.submit-section {
    margin-top: 2rem;
    display: flex;
    justify-content: flex-end;
}

.submit-button {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.875rem 2rem;
    font-weight: 600;
    font-size: 0.9375rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
}

.submit-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
}

.submit-button:active:not(:disabled) {
    transform: translateY(0);
}

.button-icon {
    width: 1.25rem;
    height: 1.25rem;
}

/* Security Tips */
.security-tips {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 2px solid #bfdbfe;
    border-radius: 1rem;
    padding: 1.5rem;
    animation: fadeIn 0.6s ease-out 0.3s both;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.tips-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.tips-icon {
    width: 1.5rem;
    height: 1.5rem;
    color: #2563eb;
}

.tips-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1rem;
    font-weight: 600;
    color: #1e40af;
}

.tips-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.tip-item {
    display: flex;
    align-items: flex-start;
    gap: 0.625rem;
    font-size: 0.875rem;
    color: #1e40af;
    animation: slideInLeft 0.4s ease-out both;
}

.tip-item:nth-child(1) { animation-delay: 0.4s; }
.tip-item:nth-child(2) { animation-delay: 0.5s; }
.tip-item:nth-child(3) { animation-delay: 0.6s; }
.tip-item:nth-child(4) { animation-delay: 0.7s; }

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.tip-check {
    width: 1.125rem;
    height: 1.125rem;
    flex-shrink: 0;
    color: #2563eb;
    margin-top: 0.125rem;
}

/* Responsive Design */
@media (max-width: 640px) {
    .main-title {
        font-size: 1.5rem;
    }

    .submit-button {
        width: 100%;
        justify-content: center;
    }

    .submit-section {
        justify-content: stretch;
    }
}
</style>
