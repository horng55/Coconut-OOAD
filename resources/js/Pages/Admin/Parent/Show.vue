<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Parents", href: route("admin.parents.index")},
    {label: "Parent Details", href: "#"}
];

const props = defineProps({
    parent: Object,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            :title="`Parent: ${parent?.user?.full_name || 'N/A'}`"
            subtitle="View parent details and information"
            icon="fas fa-user"
            icon-color="text-indigo-500"
            icon-bg="from-indigo-500/20 to-purple-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.parents.index')"
                        class="text-indigo-500 hover:text-indigo-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Parents
                    </Link>
                    <Link
                        :href="route('admin.parents.edit', parent?.id)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-edit"></i>
                        Edit Parent
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Full Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ parent?.user?.full_name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Username</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">@{{ parent?.user?.username || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Email</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ parent?.user?.email || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Relationship</h3>
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full text-sm font-medium capitalize">
                            {{ parent?.relationship || 'N/A' }}
                        </span>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Phone Number</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ parent?.user?.phone_number || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                        <span :class="[
                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium',
                            parent?.user?.status === 'active' 
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                        ]">
                            <i :class="parent?.user?.status === 'active' ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                            {{ parent?.user?.status || 'N/A' }}
                        </span>
                    </div>

                    <div v-if="parent?.address" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Address</h3>
                        <p class="text-gray-900 dark:text-white">{{ parent.address }}</p>
                    </div>

                    <div v-if="parent?.emergency_contact" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Emergency Contact</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ typeof parent.emergency_contact === 'object' ? parent.emergency_contact.phone : parent.emergency_contact }}
                        </p>
                    </div>
                </div>

                <div v-if="parent?.students && parent.students.length > 0" class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Children</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            v-for="student in parent.students"
                            :key="student.id"
                            class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 border border-indigo-200 dark:border-indigo-800"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-indigo-900 dark:text-indigo-300">{{ student.user?.full_name }}</h4>
                                <Link
                                    :href="route('admin.students.show', student.id)"
                                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm"
                                >
                                    <i class="fas fa-eye"></i> View
                                </Link>
                            </div>
                            <p class="text-sm text-indigo-700 dark:text-indigo-400">ID: {{ student.student_id }}</p>
                            <p class="text-sm text-indigo-700 dark:text-indigo-400">
                                Status: <span :class="[
                                    'font-medium',
                                    student.status === 'active' ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400'
                                ]">{{ student.status }}</span>
                            </p>
                            <div v-if="student.enrollments && student.enrollments.length > 0" class="mt-2">
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">Enrolled Classes:</p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <span
                                        v-for="enrollment in student.enrollments"
                                        :key="enrollment.id"
                                        class="text-xs bg-indigo-100 dark:bg-indigo-900/40 text-indigo-800 dark:text-indigo-300 px-2 py-1 rounded"
                                    >
                                        {{ enrollment.class_model?.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

