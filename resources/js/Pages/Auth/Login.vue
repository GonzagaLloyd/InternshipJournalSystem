<script setup>
import { ref } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

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
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-12">
            <!-- Email Input -->
            <div class="relative group">
                <TextInput
                    id="email"
                    type="email"
                    class="peer block w-full bg-transparent !border-none !ring-0 !shadow-none px-6 py-4 transition-all duration-300 rounded-2xl outline-none text-white z-20"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder=" "
                />
                
                <!-- Notched Border -->
                <fieldset class="absolute inset-0 border border-white/30 rounded-2xl pointer-events-none transition-all duration-300 peer-focus:border-indigo-400 group-hover:border-white/50 z-10">
                    <legend class="ml-8 px-0 transition-all duration-300 max-w-[0.01px] invisible whitespace-nowrap
                                   peer-focus:px-2 peer-focus:max-w-full
                                   [:not(:placeholder-shown)]:px-2 [:not(:placeholder-shown)]:max-w-full">
                        <span class="text-[10px] px-2 opacity-0 select-none">Email Address</span>
                    </legend>
                </fieldset>

                <label 
                    for="email" 
                    class="absolute left-10 top-1/2 -translate-y-1/2 text-white/70 transition-all duration-300 pointer-events-none whitespace-nowrap z-30 font-medium text-sm
                           peer-focus:-top-2 peer-focus:text-xs peer-focus:text-indigo-400 peer-focus:font-bold peer-focus:tracking-wider
                           [:not(:placeholder-shown)]:-top-2 [:not(:placeholder-shown)]:text-xs [:not(:placeholder-shown)]:text-indigo-400 [:not(:placeholder-shown)]:font-bold [:not(:placeholder-shown)]:tracking-wider"
                >
                    Email Address
                </label>
                <InputError class="mt-2 text-red-100/90 text-xs font-semibold drop-shadow-sm" :message="form.errors.email" />
            </div>

            <!-- Password Input -->
            <div class="relative group">
                <div class="relative">
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="peer block w-full bg-transparent !border-none !ring-0 !shadow-none px-6 py-4 transition-all duration-300 rounded-2xl outline-none text-white z-20"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder=" "
                    />

                    <!-- Notched Border -->
                    <fieldset class="absolute inset-0 border border-white/30 rounded-2xl pointer-events-none transition-all duration-300 peer-focus:border-indigo-400 group-hover:border-white/50 z-10">
                        <legend class="ml-8 px-0 transition-all duration-300 max-w-[0.01px] invisible whitespace-nowrap
                                       peer-focus:px-2 peer-focus:max-w-full
                                       [:not(:placeholder-shown)]:px-2 [:not(:placeholder-shown)]:max-w-full">
                            <span class="text-[10px] px-2 opacity-0 select-none">Password</span>
                        </legend>
                    </fieldset>

                    <label 
                        for="password" 
                        class="absolute left-10 top-1/2 -translate-y-1/2 text-white/70 transition-all duration-300 pointer-events-none whitespace-nowrap z-30 font-medium text-sm
                               peer-focus:-top-2 peer-focus:text-xs peer-focus:text-indigo-400 peer-focus:font-bold peer-focus:tracking-wider
                               [:not(:placeholder-shown)]:-top-2 [:not(:placeholder-shown)]:text-xs [:not(:placeholder-shown)]:text-indigo-400 [:not(:placeholder-shown)]:font-bold [:not(:placeholder-shown)]:tracking-wider"
                    >
                        Password
                    </label>
                    <button 
                        v-if="form.password"
                        type="button" 
                        @click="showPassword = !showPassword"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-colors focus:outline-none p-2 rounded-full hover:bg-white/10 z-30"
                    >
                        <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <InputError class="mt-2 text-red-100/80 text-xs" :message="form.errors.password" />
            </div>

            <div class="mt-8 flex items-center justify-between">
                <label class="flex items-center cursor-pointer group">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-white/30 bg-white/20 text-indigo-400 transition-all shadow-sm" />
                    <span class="ms-3 text-sm text-gray-400 group-hover:text-white transition-colors"
                        >Stay logged in</span
                    >
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors font-medium border-b border-transparent hover:border-indigo-300"
                >
                    Forgot Password?
                </Link>
            </div>

            <div class="mt-12">
                <PrimaryButton
                    class="w-full justify-center py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-lg tracking-[0.2em] shadow-[0_20px_40px_-15px_rgba(79,70,229,0.5)] transition-all duration-300 rounded-2xl active:scale-95 group overflow-hidden relative"
                    :class="{ 'opacity-50 pointer-events-none': form.processing }"
                    :disabled="form.processing"
                >
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    <span v-if="form.processing" class="mr-3 h-5 w-5 animate-spin border-2 border-white border-t-transparent rounded-full"></span>
                    LOG IN
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
fieldset {
    margin-top: -1px;
}
</style>
```
