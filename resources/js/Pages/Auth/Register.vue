<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/Form/InputError.vue';
import InputLabel from '@/Components/Form/InputLabel.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import TextInput from '@/Components/Form/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div class="space-y-6">
                <div class="space-y-2">
                    <InputLabel for="name" value="The Scribe's Name" />
                    <div class="relative bg-black/30 border border-white/5 rounded-2xl overflow-hidden focus-within:border-[#8C6A4A]/60 transition-all px-4 py-1">
                        <TextInput
                            id="name"
                            type="text"
                            class="block w-full !text-[#C9B79C] !placeholder-[#8C6A4A]/40 !text-lg"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="How shall you be known?"
                        />
                    </div>
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <InputLabel for="email" value="The Ledger Address" />
                    <div class="relative bg-black/30 border border-white/5 rounded-2xl overflow-hidden focus-within:border-[#8C6A4A]/60 transition-all px-4 py-1">
                        <TextInput
                            id="email"
                            type="email"
                            class="block w-full !text-[#C9B79C] !placeholder-[#8C6A4A]/40 !text-lg"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="your@echo.com"
                        />
                    </div>
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <InputLabel for="password" value="Master Seal" />
                    <div class="relative bg-black/30 border border-white/5 rounded-2xl overflow-hidden focus-within:border-[#8C6A4A]/60 transition-all px-4 py-1">
                        <TextInput
                            id="password"
                            type="password"
                            class="block w-full !text-[#C9B79C] !placeholder-[#8C6A4A]/40 !text-lg"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                        />
                    </div>
                    <InputError :message="form.errors.password" />
                </div>

                <div class="space-y-2">
                    <InputLabel
                        for="password_confirmation"
                        value="Confirm Master Seal"
                    />
                    <div class="relative bg-black/30 border border-white/5 rounded-2xl overflow-hidden focus-within:border-[#8C6A4A]/60 transition-all px-4 py-1">
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="block w-full !text-[#C9B79C] !placeholder-[#8C6A4A]/40 !text-lg"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                        />
                    </div>
                    <InputError :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="mt-12 flex flex-col sm:flex-row items-center justify-between gap-6">
                <Link
                    :href="route('login')"
                    class="text-sm text-[#8C6A4A] hover:text-[#C9B79C] transition-colors font-serif border-b border-transparent hover:border-[#C9B79C]"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="w-full sm:w-auto"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

