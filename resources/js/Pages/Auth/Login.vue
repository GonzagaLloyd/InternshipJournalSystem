<script setup>
import { ref } from 'vue';
import Checkbox from '@/Components/Form/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/Form/InputError.vue';
import InputLabel from '@/Components/Form/InputLabel.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import TextInput from '@/Components/Form/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import TomeLoader from '@/Components/UI/TomeLoader.vue';

const isLoggingIn = ref(false);

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    isLoggingIn.value = true;
    
    // Explicit 1.5s delay for the transition
    setTimeout(() => {
        form.post(route('login'), {
            onFinish: () => {
                form.reset('password');
                if (Object.keys(form.errors).length > 0) {
                    isLoggingIn.value = false;
                }
            },
            onError: () => isLoggingIn.value = false
        });
    }, 1500);
};
</script>

<template>
    <Head title="Log in" />
    <TomeLoader :show="isLoggingIn" message="Consulting the Custodian..." />

    <GuestLayout>
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6 sm:space-y-8">
            <!-- Email Input -->
            <div class="relative group">
                <div class="relative h-[64px] sm:h-[72px]">
                    <TextInput
                        id="email"
                        type="email"
                        class="peer block w-full h-full bg-transparent !border-none !ring-0 !shadow-none px-6 sm:px-8 transition-all duration-300 rounded-[1.25rem] outline-none text-white z-20 text-[16px] sm:text-[18px]"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder=" "
                    />
                    
                    <!-- Notched Border -->
                    <fieldset 
                        class="absolute inset-0 border rounded-[1.25rem] pointer-events-none transition-all duration-300 z-10"
                        :class="[
                            form.errors.email 
                                ? 'border-[#A65D50]/50 group-hover:border-[#A65D50]/80 peer-focus:border-[#A65D50]' 
                                : 'border-white/10 group-hover:border-white/20 peer-focus:border-[#8C6A4A]/60'
                        ]"
                    >
                        <legend 
                            class="ml-6 sm:ml-8 px-0 transition-all duration-300 max-w-[0.01px] invisible whitespace-nowrap"
                            :class="{ 'floating-notch': form.email || form.errors.email }"
                        >
                            <span class="text-[10px] sm:text-[12px] px-2 opacity-0 select-none font-bold uppercase tracking-[0.2em] font-serif">Email Address</span>
                        </legend>
                    </fieldset>

                    <label 
                        for="email" 
                        class="absolute left-8 sm:left-10 top-1/2 -translate-y-1/2 text-[#C9B79C]/60 transition-all duration-300 pointer-events-none whitespace-nowrap z-30 font-medium text-[16px] sm:text-[18px] font-serif"
                        :class="[
                            form.email || form.errors.email ? 'floating-label' : '',
                            form.errors.email ? '!text-[#A65D50]' : ''
                        ]"
                    >
                        Email Address
                    </label>
                </div>
                <div class="min-h-[16px] sm:min-h-[20px]">
                    <InputError :message="form.errors.email" />
                </div>
            </div>

            <!-- Password Input -->
            <div class="relative group">
                <div class="relative h-[64px] sm:h-[72px]">
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="peer block w-full h-full bg-transparent !border-none !ring-0 !shadow-none px-6 sm:px-8 transition-all duration-300 rounded-[1.25rem] outline-none text-white z-20 text-[16px] sm:text-[18px]"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder=" "
                    />

                    <!-- Notched Border -->
                    <fieldset 
                        class="absolute inset-0 border rounded-[1.25rem] pointer-events-none transition-all duration-300 z-10"
                        :class="[
                            form.errors.password
                                ? 'border-[#A65D50]/50 group-hover:border-[#A65D50]/80 peer-focus:border-[#A65D50]' 
                                : 'border-white/10 group-hover:border-white/20 peer-focus:border-[#8C6A4A]/60'
                        ]"
                    >
                        <legend 
                            class="ml-6 sm:ml-8 px-0 transition-all duration-300 max-w-[0.01px] invisible whitespace-nowrap"
                            :class="{ 'floating-notch': form.password || form.errors.password }"
                        >
                            <span class="text-[10px] sm:text-[12px] px-2 opacity-0 select-none font-bold uppercase tracking-[0.2em] font-serif">Password</span>
                        </legend>
                    </fieldset>

                    <label 
                        for="password" 
                        class="absolute left-8 sm:left-10 top-1/2 -translate-y-1/2 text-[#C9B79C]/60 transition-all duration-300 pointer-events-none whitespace-nowrap z-30 font-medium text-[16px] sm:text-[18px] font-serif"
                        :class="[
                            form.password || form.errors.password ? 'floating-label' : '',
                            form.errors.password ? '!text-[#A65D50]' : ''
                        ]"
                    >
                        Password
                    </label>
                    <button 
                        v-if="form.password"
                        type="button" 
                        @click="showPassword = !showPassword"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-white/30 hover:text-white transition-colors focus:outline-none p-2 rounded-full hover:bg-white/10 z-30 text-xs sm:text-base"
                    >
                        <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <div class="min-h-[16px] sm:min-h-[20px]">
                    <InputError :message="form.errors.password" />
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-2">
                <label class="flex items-center cursor-pointer group w-full sm:w-auto font-serif">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-3 text-sm text-[#C9B79C]/60 group-hover:text-[#C9B79C]/70 transition-colors whitespace-nowrap"
                        >Stay logged in</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-[#8C6A4A] hover:text-[#C9B79C] transition-colors font-serif border-b border-transparent hover:border-[#C9B79C]"
                >
                    Forgot Password?
                </Link>
            </div>

            <div class="mt-6 sm:mt-8">
                <PrimaryButton
                    class="w-full justify-center py-3 sm:py-4 font-serif"
                    :class="{ 'opacity-50 pointer-events-none': form.processing || isLoggingIn }"
                    :disabled="form.processing || isLoggingIn"
                >
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    <span v-if="form.processing || isLoggingIn" class="mr-3 h-5 w-5 animate-spin border-2 border-[#1B1B1B] border-t-transparent rounded-full font-serif"></span>
                    LOG IN
                </PrimaryButton>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-[#C9B79C]/60 font-serif">
                    Don't have an account? 
                    <Link
                        :href="route('register')"
                        class="text-[#8C6A4A] hover:text-[#C9B79C] transition-colors font-serif border-b border-transparent hover:border-[#C9B79C] ml-1 font-bold tracking-wide"
                    >
                        REGISTER
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
.peer:focus ~ fieldset legend,
.floating-notch {
    max-width: 100% !important;
    padding: 0 12px !important;
}

.peer:focus ~ label,
.floating-label {
    top: 0 !important;
    font-size: 12px !important;
    color: #8C6A4A !important;
    font-weight: 700 !important;
    letter-spacing: 0.2em !important;
    text-transform: uppercase !important;
}

fieldset {
    margin-top: -1px;
}
</style>

