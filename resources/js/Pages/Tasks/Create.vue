<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    description: '',
    status: 'pending',
    due_date: '',
    priority: 'medium',
    category_id: '',
});

const props = defineProps({
    categories: Array,
});

const submit = () => {
    form.post(route('tasks.store'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Create Task" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create New Task</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="max-w-xl">
                            <div>
                                <InputLabel for="title" value="Title" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.title"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="description" value="Description" />
                                <textarea
                                    id="description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    v-model="form.description"
                                    rows="4"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="category" value="category" />
                                <select
                                    id="category"
                                    v-model="form.category_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >   
                                    <option value="">Select a Category</option>
                                    <option v-for="category in categories"
                                            :key="category._id" 
                                            :value="String(category._id)">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.category_id" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="status" value="Status" />
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="priority" value="priority" />
                                <select
                                    id="priority"
                                    v-model="form.priority"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="low">low</option>
                                    <option value="medium">medium</option>
                                    <option value="high">high</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.priority" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="due_date" value="Due Date" />
                                <TextInput
                                    id="due_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.due_date"
                                />
                                <InputError class="mt-2" :message="form.errors.due_date" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link :href="route('tasks.index')" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                    Cancel
                                </Link>
                                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Create Task
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
