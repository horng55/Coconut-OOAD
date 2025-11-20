<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Announcements", href: route("admin.announcements.index")},
    {label: "Announcement Details", href: "#"}
];

const props = defineProps({
    announcement: Object,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            :title="announcement?.title || 'Announcement Details'"
            subtitle="View announcement information and details"
            icon="fas fa-bullhorn"
            icon-color="text-rose-500"
            icon-bg="from-rose-500/20 to-pink-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.announcements.index')"
                        class="text-rose-500 hover:text-rose-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Announcements
                    </Link>
                    <Link
                        :href="route('admin.announcements.edit', announcement?.id)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-edit"></i>
                        Edit Announcement
                    </Link>
                </div>

                <!-- Announcement Header -->
                <div class="bg-gradient-to-r from-rose-50 to-pink-50 dark:from-rose-900/20 dark:to-pink-900/20 rounded-xl p-6 border border-rose-200 dark:border-rose-800">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ announcement?.title || 'N/A' }}
                                </h1>
                                <span v-if="announcement?.is_pinned" class="text-rose-500" title="Pinned">
                                    <i class="fas fa-thumbtack"></i>
                                </span>
                            </div>
                            <div class="flex items-center gap-4 flex-wrap mt-3">
                                <span :class="[
                                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                    announcement?.priority === 'urgent' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' :
                                    announcement?.priority === 'high' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' :
                                    announcement?.priority === 'normal' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                    'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                                ]">
                                    <i :class="
                                        announcement?.priority === 'urgent' ? 'fas fa-exclamation-triangle' :
                                        announcement?.priority === 'high' ? 'fas fa-exclamation-circle' :
                                        'fas fa-info-circle'
                                    "></i>
                                    {{ announcement?.priority || 'N/A' }}
                                </span>
                                <div class="flex flex-wrap gap-2">
                                    <span 
                                        v-for="audience in (Array.isArray(announcement?.target_audience) ? announcement.target_audience : [announcement?.target_audience])" 
                                        :key="audience"
                                        class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full text-xs font-medium capitalize"
                                    >
                                        <i class="fas fa-users"></i>
                                        {{ audience }}
                                    </span>
                                    <span v-if="!announcement?.target_audience || (Array.isArray(announcement?.target_audience) && announcement.target_audience.length === 0)" class="text-gray-500 dark:text-gray-400 text-xs">
                                        N/A
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Announcement Content -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-file-alt text-rose-500"></i>
                            Content
                        </h3>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
                            <p class="text-gray-900 dark:text-white whitespace-pre-wrap leading-relaxed">
                                {{ announcement?.content || 'No content available.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Class Information -->
                    <div v-if="announcement?.class_model" class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-book-reader text-amber-500"></i>
                            Class Information
                        </h3>
                    </div>

                    <div v-if="announcement?.class_model" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Class Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ announcement?.class_model?.name || 'N/A' }}</p>
                    </div>

                    <div v-if="announcement?.class_model" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Class Code</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">{{ announcement?.class_model?.code || 'N/A' }}</p>
                    </div>

                    <div v-if="announcement?.class_model" class="md:col-span-2 flex gap-3">
                        <Link
                            :href="route('admin.classes.show', announcement?.class_model?.id)"
                            class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-eye"></i>
                            View Full Class Details
                        </Link>
                    </div>

                    <!-- Announcement Details -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            Announcement Details
                        </h3>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Publish Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ announcement?.publish_date ? new Date(announcement.publish_date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A' }}
                        </p>
                    </div>

                    <div v-if="announcement?.expiry_date" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Expiry Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ new Date(announcement.expiry_date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Created By</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ announcement?.created_by_user?.name || 'N/A' }}</p>
                        <p v-if="announcement?.created_by_user?.email" class="text-sm text-gray-600 dark:text-gray-400">{{ announcement.created_by_user.email }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Created At</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ announcement?.created_at ? new Date(announcement.created_at).toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Is Pinned</h3>
                        <span :class="[
                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium',
                            announcement?.is_pinned 
                                ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-800 dark:text-rose-300'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
                        ]">
                            <i :class="announcement?.is_pinned ? 'fas fa-thumbtack' : 'fas fa-circle'"></i>
                            {{ announcement?.is_pinned ? 'Yes' : 'No' }}
                        </span>
                    </div>

                    <div v-if="announcement?.updated_at" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Last Updated</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ new Date(announcement.updated_at).toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                        </p>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

