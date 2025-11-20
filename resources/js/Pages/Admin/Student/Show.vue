<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Students", href: route("admin.students.index")},
    {label: "Student Details", href: "#"}
];

const props = defineProps({
    student: Object,
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            :title="`Student: ${student?.user?.full_name || 'N/A'}`"
            subtitle="View student details and information"
            icon="fas fa-user-graduate"
            icon-color="text-emerald-500"
            icon-bg="from-emerald-500/20 to-teal-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.students.index')"
                        class="text-emerald-500 hover:text-emerald-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Students
                    </Link>
                    <Link
                        :href="route('admin.students.edit', student?.id)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-edit"></i>
                        Edit Student
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">First Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ student?.user?.first_name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Last Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ student?.user?.last_name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Username</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">@{{ student?.user?.username || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Email</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ student?.user?.email || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Student ID</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ student?.student_id || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Admission Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ student?.admission_date ? new Date(student.admission_date).toLocaleDateString() : 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                        <span :class="[
                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium',
                            student?.status === 'active' 
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                        ]">
                            <i :class="student?.status === 'active' ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                            {{ student?.status || 'N/A' }}
                        </span>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Gender</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ student?.user?.gender || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Phone Number</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ student?.user?.phone_number || 'N/A' }}</p>
                    </div>

                    <div v-if="student?.parent" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Parent</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ student.parent.user?.full_name || 'N/A' }}</p>
                        <p v-if="student.parent.user?.email" class="text-sm text-gray-600 dark:text-gray-400">{{ student.parent.user.email }}</p>
                    </div>

                    <div v-if="student?.medical_info" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Medical Information</h3>
                        <p class="text-gray-900 dark:text-white">{{ student.medical_info }}</p>
                    </div>
                </div>

                <div v-if="student?.enrollments && student.enrollments.length > 0" class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Enrolled Classes</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            v-for="enrollment in student.enrollments"
                            :key="enrollment.id"
                            class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-4 border border-emerald-200 dark:border-emerald-800"
                        >
                            <h4 class="font-semibold text-emerald-900 dark:text-emerald-300">{{ enrollment.class_model?.name }}</h4>
                            <p class="text-sm text-emerald-700 dark:text-emerald-400">Code: {{ enrollment.class_model?.code }}</p>
                            <p class="text-sm text-emerald-700 dark:text-emerald-400 mt-1">
                                Status: <span :class="[
                                    'font-medium',
                                    enrollment.status === 'active' ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400'
                                ]">{{ enrollment.status }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="student?.grades && student.grades.length > 0" class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Grades</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Assessment</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Class</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Score</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Grade</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="grade in student.grades" :key="grade.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                        <div>
                                            <span class="font-medium">{{ grade.assessment_name }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block capitalize">{{ grade.assessment_type }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">{{ grade.class_model?.name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                                        {{ grade.score }}/{{ grade.max_score }} ({{ grade.percentage }}%)
                                    </td>
                                    <td class="px-4 py-2">
                                        <span :class="[
                                            'inline-flex items-center px-2 py-1 rounded text-xs font-bold',
                                            grade.letter_grade === 'A' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                                            grade.letter_grade === 'B' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                            grade.letter_grade === 'C' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' :
                                            grade.letter_grade === 'D' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' :
                                            'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                        ]">
                                            {{ grade.letter_grade }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                                        {{ grade.assessment_date ? new Date(grade.assessment_date).toLocaleDateString() : 'N/A' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Attendance</h3>
                    <div v-if="student?.attendances && student.attendances.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div
                            v-for="attendance in student.attendances"
                            :key="attendance.id"
                            class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ attendance.class_model?.name || 'N/A' }}</span>
                                <span :class="[
                                    'inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium',
                                    attendance.status === 'present' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                                    attendance.status === 'late' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' :
                                    attendance.status === 'excused' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                    'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                ]">
                                    <i :class="
                                        attendance.status === 'present' ? 'fas fa-check' :
                                        attendance.status === 'late' ? 'fas fa-clock' :
                                        attendance.status === 'excused' ? 'fas fa-info' :
                                        'fas fa-times'
                                    "></i>
                                    {{ attendance.status }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ attendance.date ? new Date(attendance.date).toLocaleDateString() : 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <div v-else class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 text-center">
                        <i class="fas fa-clipboard-check text-gray-400 dark:text-gray-500 text-3xl mb-2"></i>
                        <p class="text-gray-600 dark:text-gray-400">No attendance records found for this student.</p>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

