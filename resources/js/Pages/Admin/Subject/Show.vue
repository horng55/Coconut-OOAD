<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Subjects", href: route("admin.subjects.index")},
    {label: "Subject Details", href: "#"}
];

const props = defineProps({
    subject: Object,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            :title="`Subject: ${subject?.name || 'N/A'}`"
            subtitle="View subject details and information"
            icon="fas fa-book"
            icon-color="text-green-500"
            icon-bg="from-green-500/20 to-emerald-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.subjects.index')"
                        class="text-green-500 hover:text-green-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Subjects
                    </Link>
                    <Link
                        :href="route('admin.subjects.edit', subject?.id)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-edit"></i>
                        Edit Subject
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Subject Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ subject?.name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Subject Code</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">{{ subject?.code || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                        <span :class="[
                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium',
                            subject?.status === 'active' 
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                        ]">
                            <i :class="subject?.status === 'active' ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                            {{ subject?.status || 'N/A' }}
                        </span>
                    </div>

                    <div v-if="subject?.description" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ subject.description }}</p>
                    </div>
                </div>

                <div v-if="subject?.classes && subject.classes.length > 0" class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Classes Using This Subject</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            v-for="classItem in subject.classes"
                            :key="classItem.id"
                            class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800"
                        >
                            <h4 class="font-semibold text-green-900 dark:text-green-300">{{ classItem.name }}</h4>
                            <p class="text-sm text-green-700 dark:text-green-400">Code: {{ classItem.code }}</p>
                            <Link
                                :href="route('admin.classes.show', classItem.id)"
                                class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 text-sm mt-2 inline-block"
                            >
                                <i class="fas fa-eye"></i> View Class
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

