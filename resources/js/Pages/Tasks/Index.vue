<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3'; 
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    tasks: Array,
    categories: Array,
    filters: Object,
});

// We create a local "reactive" object for the filters
const filterForm = ref({
    status: props.filters.status || '',
    category_id: props.filters.category_id || '',
});
// This "watches" our filterForm. If it changes, we send a request!
watch(filterForm, (value) => {
    router.get(route('tasks.index'), value, {
        preserveState: true, // Keeps you on the same page
        replace: true,       // Updates the URL without making a new history entry
    });
}, { deep: true });

const form = useForm({});

const deleteTask = (id) => {
    if (confirm('Are you sure you want to delete this task?')) {
        form.delete(route('tasks.destroy', id));
    }
};
</script>

<template>
    <Head title="Tasks" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Task Management</h2>
                <Link
                    :href="route('tasks.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                >
                    + Create New Task
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 border-b border-gray-200">
                        <div v-if="tasks.length === 0" class="text-center py-8 text-gray-500">
                            No tasks found. Start by creating one!
                        </div>
                        <div v-else class="overflow-x-auto">
                            <!-- Filter Bar -->
<div class="mb-6 flex gap-4 bg-gray-50 p-4 rounded-lg">
    <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700">Filter by Status</label>
        <select v-model="filterForm.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>
    </div>

    <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700">Filter by Category</label>
        <select v-model="filterForm.category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category._id" :value="category._id">
                {{ category.name }}
            </option>
        </select>
    </div>
    
    <div class="flex items-end">
        <button @click="filterForm.status = ''; filterForm.category_id = '';" class="text-sm text-indigo-600 hover:text-indigo-900 mb-2">
            Clear Filters
        </button>
    </div>
</div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="task in tasks" :key="task._id">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ task.title }}</div>
                                            <div class="text-xs text-gray-500 truncate max-w-xs">{{ task.description }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="{
                                                    'bg-yellow-100 text-yellow-800': task.status === 'pending',
                                                    'bg-blue-100 text-blue-800': task.status === 'in_progress',
                                                    'bg-green-100 text-green-800': task.status === 'completed'
                                                }"
                                            >
                                                {{ task.status?.replace('_', ' ') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ task.category?.name || 'Uncategorized' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ task.due_date ? new Date(task.due_date).toLocaleDateString() : 'No date' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('tasks.edit', task._id)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</Link>
                                            <button @click="deleteTask(task._id)" class="text-red-600 hover:text-red-900">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
