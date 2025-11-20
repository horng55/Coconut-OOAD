<script setup>
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Roles", href: route("admin.roles.index")},
    {label: "Create Role", href: route("admin.roles.create")}
];

const form = useForm({
    name: '',
    slug: '',
    description: '',
    status: 'active',
});

const submit = () => {
    form.post(route('admin.roles.store'), {
        onSuccess: () => {
            router.visit(route('admin.roles.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Create New Role"
            subtitle="Add a new role to the system"
            icon="fas fa-user-shield"
            icon-color="text-purple-500"
            icon-bg="from-purple-500/20 to-indigo-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Role Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="e.g., Administrator, Teacher, Student"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        />
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Slug <span class="text-gray-500 text-xs">(Auto-generated if empty)</span>
                        </label>
                        <input
                            v-model="form.slug"
                            type="text"
                            placeholder="e.g., administrator, teacher, student"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50 font-mono lowercase"
                        />
                        <div v-if="form.errors.slug" class="text-red-500 text-sm mt-1">{{ form.errors.slug }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.status"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            placeholder="Enter role description (optional)"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        ></textarea>
                        <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.roles.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-save"></i>
                        {{ form.processing ? 'Creating...' : 'Create Role' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

