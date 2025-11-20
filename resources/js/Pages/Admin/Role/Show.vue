<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Roles", href: route("admin.roles.index")},
    {label: "Role Details", href: "#"}
];

const props = defineProps({
    role: Object,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            :title="`Role: ${role?.name || 'N/A'}`"
            subtitle="View role details and information"
            icon="fas fa-user-shield"
            icon-color="text-purple-500"
            icon-bg="from-purple-500/20 to-indigo-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.roles.index')"
                        class="text-purple-500 hover:text-purple-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Roles
                    </Link>
                    <Link
                        :href="route('admin.roles.edit', role?.id)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-edit"></i>
                        Edit Role
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Role Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ role?.name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Slug</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">{{ role?.slug || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                        <span :class="[
                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium',
                            role?.status === 'active' 
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                        ]">
                            <i :class="role?.status === 'active' ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                            {{ role?.status || 'N/A' }}
                        </span>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Users Count</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ role?.users_count || 0 }} user(s)</p>
                    </div>

                    <div v-if="role?.description" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ role.description }}</p>
                    </div>
                </div>

                <div v-if="role?.users && role.users.length > 0" class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Users with This Role</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            v-for="user in role.users"
                            :key="user.id"
                            class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800"
                        >
                            <h4 class="font-semibold text-purple-900 dark:text-purple-300">{{ user.full_name || user.email }}</h4>
                            <p class="text-sm text-purple-700 dark:text-purple-400">{{ user.email }}</p>
                            <p v-if="user.type" class="text-xs text-purple-600 dark:text-purple-500 mt-1">Type: {{ user.type }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

