<script setup>
import {Link} from "@inertiajs/vue3";
import StudentLayout from "../../../Layouts/StudentLayout.vue";

const title = [
    {label: "Announcements", href: route("student.announcements.index")},
    {label: "Announcement Details", href: "#"}
];

const props = defineProps({
    announcement: Object,
});
</script>

<template>
    <StudentLayout :title="title">
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="mb-6">
                    <Link
                        :href="route('student.announcements.index')"
                        class="text-emerald-500 hover:text-emerald-600 flex items-center gap-2 mb-4"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Announcements
                    </Link>
                </div>

                <div class="space-y-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                            {{ announcement?.title || 'N/A' }}
                        </h1>
                        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-6">
                            <span v-if="announcement?.class_model" class="flex items-center gap-1">
                                <i class="fas fa-book"></i>
                                {{ announcement.class_model.name }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-calendar"></i>
                                {{ announcement?.publish_date || 'N/A' }}
                            </span>
                            <span v-if="announcement?.created_by_user" class="flex items-center gap-1">
                                <i class="fas fa-user"></i>
                                {{ announcement.created_by_user.full_name }}
                            </span>
                        </div>
                    </div>

                    <div class="prose dark:prose-invert max-w-none">
                        <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                            {{ announcement?.content || 'No content available.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

