<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Teachers", href: route("admin.teachers.index")},
    {label: "Teacher Details", href: "#"}
];

const props = defineProps({
    teacher: Object,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            :title="`Teacher: ${teacher?.user?.full_name || 'N/A'}`"
            subtitle="View teacher details and information"
            icon="fas fa-user"
            icon-color="text-blue-500"
            icon-bg="from-blue-500/20 to-cyan-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.teachers.index')"
                        class="text-blue-500 hover:text-blue-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Teachers
                    </Link>
                    <Link
                        :href="route('admin.teachers.edit', teacher?.id)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-edit"></i>
                        Edit Teacher
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Full Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ teacher?.user?.full_name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Username</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">@{{ teacher?.user?.username || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Email</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ teacher?.user?.email || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Employee ID</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ teacher?.employee_id || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Subject Specialization</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ teacher?.subject_specialization || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Qualification</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ teacher?.qualification || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Hire Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ teacher?.hire_date ? new Date(teacher.hire_date).toLocaleDateString() : 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                        <span :class="[
                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium',
                            teacher?.status === 'active' 
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                        ]">
                            <i :class="teacher?.status === 'active' ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                            {{ teacher?.status || 'N/A' }}
                        </span>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Gender</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ teacher?.user?.gender || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Phone Number</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ teacher?.user?.phone_number || 'N/A' }}</p>
                    </div>
                </div>

                <div v-if="teacher?.classes && teacher.classes.length > 0" class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assigned Classes</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            v-for="classItem in teacher.classes"
                            :key="classItem.id"
                            class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800"
                        >
                            <h4 class="font-semibold text-blue-900 dark:text-blue-300">{{ classItem.name }}</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-400">Code: {{ classItem.code }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

