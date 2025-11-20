<script setup>
import {ref, computed} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Reports", href: route("admin.reports.index")},
    {label: "Generate Report", href: route("admin.reports.create")}
];

const props = defineProps({
    classes: Array,
    students: Array,
    teachers: Array,
    defaultAcademicYear: String,
});

const selectedReportType = ref('');

const reportTypes = [
    {
        value: 'student_performance',
        label: 'Student Performance Report',
        description: 'Generate detailed academic performance and attendance report for a specific student',
        route: 'admin.reports.generate.student-performance',
    },
    {
        value: 'class_performance',
        label: 'Class Performance Report',
        description: 'Generate overall class performance report with student rankings',
        route: 'admin.reports.generate.class-performance',
    },
    {
        value: 'attendance',
        label: 'Attendance Report',
        description: 'Generate attendance statistics and records',
        route: 'admin.reports.generate.attendance',
    },
    {
        value: 'grade_distribution',
        label: 'Grade Distribution Report',
        description: 'Generate grade distribution and statistics',
        route: 'admin.reports.generate.grade-distribution',
    },
    {
        value: 'teacher_workload',
        label: 'Teacher Workload Report',
        description: 'Generate teacher workload and class assignments report',
        route: 'admin.reports.generate.teacher-workload',
    },
    {
        value: 'enrollment',
        label: 'Enrollment Report',
        description: 'Generate enrollment statistics by class and academic year',
        route: 'admin.reports.generate.enrollment',
    },
];

// Forms for each report type
const studentPerformanceForm = useForm({
    student_id: '',
    class_id: '',
    academic_year: props.defaultAcademicYear || '',
    start_date: '',
    end_date: '',
});

const classPerformanceForm = useForm({
    class_id: '',
    academic_year: props.defaultAcademicYear || '',
});

const attendanceForm = useForm({
    class_id: '',
    student_id: '',
    start_date: '',
    end_date: '',
    status: '',
});

const gradeDistributionForm = useForm({
    class_id: '',
    subject_id: '',
    assessment_type: '',
    academic_year: props.defaultAcademicYear || '',
});

const teacherWorkloadForm = useForm({
    teacher_id: '',
    academic_year: props.defaultAcademicYear || '',
});

const enrollmentForm = useForm({
    academic_year: props.defaultAcademicYear || '',
    class_id: '',
});

const selectReportType = (type) => {
    selectedReportType.value = type.value;
};

const generateReport = () => {
    const type = reportTypes.find(t => t.value === selectedReportType.value);
    if (!type) return;

    let form;
    switch (selectedReportType.value) {
        case 'student_performance':
            form = studentPerformanceForm;
            break;
        case 'class_performance':
            form = classPerformanceForm;
            break;
        case 'attendance':
            form = attendanceForm;
            break;
        case 'grade_distribution':
            form = gradeDistributionForm;
            break;
        case 'teacher_workload':
            form = teacherWorkloadForm;
            break;
        case 'enrollment':
            form = enrollmentForm;
            break;
        default:
            return;
    }

    form.get(route(type.route), {
        preserveState: false,
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Generate Report"
            :subtitle="selectedReportType ? 'Fill in the form to generate the report' : 'Select a report type to generate'"
            icon="fas fa-chart-bar"
            icon-color="text-indigo-500"
            icon-bg="from-indigo-500/20 to-purple-500/20"
        >
            <div class="p-6">
                <!-- Report Type Selection -->
                <div v-if="!selectedReportType" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="reportType in reportTypes"
                        :key="reportType.value"
                        @click="selectReportType(reportType)"
                        class="bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg p-6 cursor-pointer hover:border-indigo-500 dark:hover:border-indigo-500 transition-all duration-200 hover:shadow-lg"
                    >
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 bg-indigo-500/10 dark:bg-indigo-500/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-indigo-500 dark:text-indigo-400 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ reportType.label }}
                            </h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            {{ reportType.description }}
                        </p>
                        <div class="flex items-center text-indigo-500 dark:text-indigo-400">
                            <span class="text-sm font-medium">Generate Report</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                </div>

                <!-- Student Performance Form -->
                <div v-if="selectedReportType === 'student_performance'" class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Student Performance Report</h2>
                        <button @click="selectedReportType = ''" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form @submit.prevent="generateReport" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student <span class="text-red-500">*</span></label>
                                <select v-model="studentPerformanceForm.student_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    <option value="">Select Student</option>
                                    <option v-for="student in students" :key="student.id" :value="student.id">
                                        {{ student.user?.full_name || student.student_id }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class</label>
                                <select v-model="studentPerformanceForm.class_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    <option value="">All Classes</option>
                                    <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                        {{ classItem.name }} ({{ classItem.code }})
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                                <input v-model="studentPerformanceForm.start_date" type="date" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                                <input v-model="studentPerformanceForm.end_date" type="date" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <button type="button" @click="selectedReportType = ''" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" :disabled="studentPerformanceForm.processing" class="px-6 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                <i v-if="studentPerformanceForm.processing" class="fas fa-spinner fa-spin"></i>
                                <i v-else class="fas fa-chart-line"></i>
                                {{ studentPerformanceForm.processing ? 'Generating...' : 'Generate Report' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Class Performance Form -->
                <div v-if="selectedReportType === 'class_performance'" class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Class Performance Report</h2>
                        <button @click="selectedReportType = ''" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form @submit.prevent="generateReport" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class <span class="text-red-500">*</span></label>
                                <select v-model="classPerformanceForm.class_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    <option value="">Select Class</option>
                                    <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                        {{ classItem.name }} ({{ classItem.code }})
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <button type="button" @click="selectedReportType = ''" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" :disabled="classPerformanceForm.processing" class="px-6 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                <i v-if="classPerformanceForm.processing" class="fas fa-spinner fa-spin"></i>
                                <i v-else class="fas fa-chart-line"></i>
                                {{ classPerformanceForm.processing ? 'Generating...' : 'Generate Report' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Attendance Form -->
                <div v-if="selectedReportType === 'attendance'" class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Attendance Report</h2>
                        <button @click="selectedReportType = ''" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form @submit.prevent="generateReport" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class</label>
                                <select v-model="attendanceForm.class_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    <option value="">All Classes</option>
                                    <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                        {{ classItem.name }} ({{ classItem.code }})
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student</label>
                                <select v-model="attendanceForm.student_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    <option value="">All Students</option>
                                    <option v-for="student in students" :key="student.id" :value="student.id">
                                        {{ student.user?.full_name || student.student_id }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date <span class="text-red-500">*</span></label>
                                <input v-model="attendanceForm.start_date" type="date" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date <span class="text-red-500">*</span></label>
                                <input v-model="attendanceForm.end_date" type="date" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                <select v-model="attendanceForm.status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    <option value="">All Status</option>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                    <option value="excused">Excused</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <button type="button" @click="selectedReportType = ''" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" :disabled="attendanceForm.processing" class="px-6 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                <i v-if="attendanceForm.processing" class="fas fa-spinner fa-spin"></i>
                                <i v-else class="fas fa-chart-line"></i>
                                {{ attendanceForm.processing ? 'Generating...' : 'Generate Report' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Grade Distribution Form -->
                <div v-if="selectedReportType === 'grade_distribution'" class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Grade Distribution Report</h2>
                        <button @click="selectedReportType = ''" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form @submit.prevent="generateReport" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class</label>
                                <select v-model="gradeDistributionForm.class_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    <option value="">All Classes</option>
                                    <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                        {{ classItem.name }} ({{ classItem.code }})
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assessment Type</label>
                                <select v-model="gradeDistributionForm.assessment_type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    <option value="">All Types</option>
                                    <option value="quiz">Quiz</option>
                                    <option value="assignment">Assignment</option>
                                    <option value="mid-term">Mid-Term</option>
                                    <option value="final">Final Exam</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <button type="button" @click="selectedReportType = ''" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" :disabled="gradeDistributionForm.processing" class="px-6 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                <i v-if="gradeDistributionForm.processing" class="fas fa-spinner fa-spin"></i>
                                <i v-else class="fas fa-chart-line"></i>
                                {{ gradeDistributionForm.processing ? 'Generating...' : 'Generate Report' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Teacher Workload Form -->
                <div v-if="selectedReportType === 'teacher_workload'" class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Teacher Workload Report</h2>
                        <button @click="selectedReportType = ''" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form @submit.prevent="generateReport" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Teacher</label>
                                <select v-model="teacherWorkloadForm.teacher_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    <option value="">All Teachers</option>
                                    <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                        {{ teacher.user?.full_name || teacher.employee_id }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <button type="button" @click="selectedReportType = ''" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" :disabled="teacherWorkloadForm.processing" class="px-6 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                <i v-if="teacherWorkloadForm.processing" class="fas fa-spinner fa-spin"></i>
                                <i v-else class="fas fa-chart-line"></i>
                                {{ teacherWorkloadForm.processing ? 'Generating...' : 'Generate Report' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Enrollment Form -->
                <div v-if="selectedReportType === 'enrollment'" class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Enrollment Report</h2>
                        <button @click="selectedReportType = ''" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form @submit.prevent="generateReport" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Year</label>
                                <input v-model="enrollmentForm.academic_year" type="text" placeholder="e.g., 2023-2024" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class</label>
                                <select v-model="enrollmentForm.class_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    <option value="">All Classes</option>
                                    <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                        {{ classItem.name }} ({{ classItem.code }})
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <button type="button" @click="selectedReportType = ''" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" :disabled="enrollmentForm.processing" class="px-6 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                <i v-if="enrollmentForm.processing" class="fas fa-spinner fa-spin"></i>
                                <i v-else class="fas fa-chart-line"></i>
                                {{ enrollmentForm.processing ? 'Generating...' : 'Generate Report' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Back Button -->
                <div v-if="selectedReportType" class="mt-6 flex items-center justify-end">
                    <Link
                        :href="route('admin.reports.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                    >
                        Back to Reports
                    </Link>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

