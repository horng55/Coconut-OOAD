<script setup>
import {computed} from "vue";
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Classes", href: route("admin.classes.index")},
    {label: "Class Details", href: "#"}
];

const props = defineProps({
    classData: {
        type: Object,
        required: true,
    },
});

// Use a computed property to safely access the classData prop
const classInfo = computed(() => props.classData);

const classTitle = computed(() => {
    if (!classInfo.value) return 'Class Details';
    return `Class: ${classInfo.value?.name || 'N/A'}`;
});

const classId = computed(() => classInfo.value?.id);
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            :title="classTitle"
            subtitle="View class details and information"
            icon="fas fa-book-reader"
            icon-color="text-amber-500"
            icon-bg="from-amber-500/20 to-orange-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.classes.index')"
                        class="text-amber-500 hover:text-amber-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Classes
                    </Link>
                    <Link
                        v-if="classId"
                        :href="route('admin.classes.edit', classId)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-edit"></i>
                        Edit Class
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Class Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ classInfo?.name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Class Code</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">{{ classInfo?.code || 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Subjects</h3>
                        <div v-if="classInfo?.subjects && classInfo.subjects.length > 0" class="space-y-2">
                            <div v-for="subject in classInfo.subjects" :key="subject.id" class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded">
                                <div>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ subject.name || 'N/A' }}</p>
                                    <p v-if="subject.code" class="text-sm text-gray-600 dark:text-gray-400">Code: {{ subject.code }}</p>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-gray-600 dark:text-gray-400">No subjects assigned</p>
                    </div>

                    <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Teachers</h3>
                        <div v-if="classInfo?.teachers && classInfo.teachers.length > 0" class="space-y-2">
                            <div v-for="teacher in classInfo.teachers" :key="teacher.id" class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded">
                                <div>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ teacher.user?.full_name || 'N/A' }}</p>
                                    <p v-if="teacher.employee_id" class="text-sm text-gray-600 dark:text-gray-400">ID: {{ teacher.employee_id }}</p>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-gray-600 dark:text-gray-400">No teachers assigned</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Academic Year</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ classInfo?.academic_year || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Max Students</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ classInfo?.max_students || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                        <span :class="[
                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium',
                            classInfo?.status === 'active' 
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                : classInfo?.status === 'completed'
                                ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300'
                                : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                        ]">
                            <i :class="classInfo?.status === 'active' ? 'fas fa-check-circle' : classInfo?.status === 'completed' ? 'fas fa-check-double' : 'fas fa-times-circle'"></i>
                            {{ classInfo?.status || 'N/A' }}
                        </span>
                    </div>

                    <div v-if="classInfo?.description" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
                        <p class="text-gray-900 dark:text-white">{{ classData.description }}</p>
                    </div>
                </div>

                <div v-if="classInfo?.enrollments && classData.enrollments.length > 0" class="mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Enrolled Students ({{ classData.enrollments.length }})
                        </h3>
                        <Link
                            :href="route('admin.enrollments.create', { class_id: classId })"
                            class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 text-sm"
                        >
                            <i class="fas fa-plus"></i>
                            Add Student
                        </Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student Name</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student ID</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Enrollment Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr 
                                    v-for="(enrollment, index) in classData.enrollments" 
                                    :key="enrollment.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                                >
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        <div class="flex items-center justify-center w-8 h-8 bg-amber-500/10 dark:bg-amber-500/30 rounded-lg text-amber-500 dark:text-amber-400 font-semibold">
                                            {{ index + 1 }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>
                                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ enrollment.student?.user?.full_name || (enrollment.student?.user?.first_name + ' ' + enrollment.student?.user?.last_name) || 'N/A' }}</span>
                                            <p v-if="enrollment.student?.user?.email" class="text-xs text-gray-500 dark:text-gray-400">{{ enrollment.student.user.email }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 font-mono">
                                        {{ enrollment.student?.student_id || 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ enrollment.enrollment_date ? new Date(enrollment.enrollment_date).toLocaleDateString() : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span :class="[
                                            'inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium',
                                            enrollment.status === 'active' 
                                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
                                        ]">
                                            <i :class="enrollment.status === 'active' ? 'fas fa-check' : 'fas fa-pause'"></i>
                                            {{ enrollment.status || 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            <Link
                                                :href="route('admin.students.show', enrollment.student?.id)"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                                title="View Student"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div v-else class="mt-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-8 text-center">
                        <i class="fas fa-user-graduate text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400">No students enrolled in this class yet.</p>
                        <Link
                            :href="route('admin.enrollments.create', { class_id: classId })"
                            class="mt-4 inline-block bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 mx-auto"
                        >
                            <i class="fas fa-plus"></i>
                            Enroll First Student
                        </Link>
                    </div>
                </div>

                <div v-if="classInfo?.announcements && classData.announcements.length > 0" class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Class Announcements</h3>
                    <div class="space-y-3">
                        <div
                            v-for="announcement in classData.announcements"
                            :key="announcement.id"
                            class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-1">{{ announcement.title }}</h4>
                                    <p class="text-sm text-blue-700 dark:text-blue-400">{{ announcement.content }}</p>
                                    <p class="text-xs text-blue-600 dark:text-blue-500 mt-2">
                                        {{ announcement.created_at ? new Date(announcement.created_at).toLocaleDateString() : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

