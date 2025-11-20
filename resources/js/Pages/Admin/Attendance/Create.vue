<script setup>
import {ref, watch} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Attendance", href: route("admin.attendances.index")},
    {label: "Mark Attendance", href: route("admin.attendances.create")}
];

const props = defineProps({
    classes: Array,
    selectedClass: Object,
    students: Array,
    date: String,
});

const form = useForm({
    class_id: props.selectedClass?.id || '',
    date: props.date || new Date().toISOString().split('T')[0],
    attendances: [],
});

const selectedClass = ref(props.selectedClass);
const attendanceData = ref({});

// Initialize attendance data from props.students
if (props.students && props.students.length > 0) {
    props.students.forEach(student => {
        attendanceData.value[student.id] = {
            student_id: student.id,
            status: student.attendance?.status || 'present',
            notes: student.attendance?.notes || '',
        };
    });
}

watch(() => form.class_id, (newClassId) => {
    if (newClassId) {
        router.get(route('admin.attendances.create'), {
            class_id: newClassId,
            date: form.date
        }, {
            preserveState: true,
            preserveScroll: true
        });
    }
});

watch(() => form.date, (newDate) => {
    if (form.class_id) {
        router.get(route('admin.attendances.create'), {
            class_id: form.class_id,
            date: newDate
        }, {
            preserveState: true,
            preserveScroll: true
        });
    }
});

const updateAttendance = (studentId, status) => {
    if (!attendanceData.value[studentId]) {
        attendanceData.value[studentId] = {
            student_id: studentId,
            status: status,
            notes: '',
        };
    } else {
        attendanceData.value[studentId].status = status;
    }
};

const submit = () => {
    // Convert attendanceData object to array
    form.attendances = Object.values(attendanceData.value);
    
    form.post(route('admin.attendances.store'), {
        onSuccess: () => {
            router.visit(route('admin.attendances.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Mark Attendance"
            subtitle="Record attendance for students in a class"
            icon="fas fa-clipboard-check"
            icon-color="text-purple-500"
            icon-bg="from-purple-500/20 to-pink-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Class <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.class_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        >
                            <option value="">Select Class</option>
                            <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                {{ classItem.name }} ({{ classItem.code }})
                            </option>
                        </select>
                        <div v-if="form.errors.class_id" class="text-red-500 text-sm mt-1">{{ form.errors.class_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.date"
                            type="date"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                        />
                        <div v-if="form.errors.date" class="text-red-500 text-sm mt-1">{{ form.errors.date }}</div>
                    </div>
                </div>

                <div v-if="students && students.length > 0" class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Students in {{ selectedClass?.name || 'Selected Class' }}
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="student in students"
                            :key="student.id"
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ student.name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">ID: {{ student.student_id }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        @click="updateAttendance(student.id, 'present')"
                                        :class="[
                                            'px-4 py-2 rounded-lg transition-all duration-200',
                                            attendanceData[student.id]?.status === 'present'
                                                ? 'bg-green-500 text-white shadow-lg'
                                                : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-green-400'
                                        ]"
                                    >
                                        <i class="fas fa-check"></i> Present
                                    </button>
                                    <button
                                        type="button"
                                        @click="updateAttendance(student.id, 'absent')"
                                        :class="[
                                            'px-4 py-2 rounded-lg transition-all duration-200',
                                            attendanceData[student.id]?.status === 'absent'
                                                ? 'bg-red-500 text-white shadow-lg'
                                                : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-red-400'
                                        ]"
                                    >
                                        <i class="fas fa-times"></i> Absent
                                    </button>
                                    <button
                                        type="button"
                                        @click="updateAttendance(student.id, 'late')"
                                        :class="[
                                            'px-4 py-2 rounded-lg transition-all duration-200',
                                            attendanceData[student.id]?.status === 'late'
                                                ? 'bg-yellow-500 text-white shadow-lg'
                                                : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-yellow-400'
                                        ]"
                                    >
                                        <i class="fas fa-clock"></i> Late
                                    </button>
                                    <button
                                        type="button"
                                        @click="updateAttendance(student.id, 'excused')"
                                        :class="[
                                            'px-4 py-2 rounded-lg transition-all duration-200',
                                            attendanceData[student.id]?.status === 'excused'
                                                ? 'bg-blue-500 text-white shadow-lg'
                                                : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-blue-400'
                                        ]"
                                    >
                                        <i class="fas fa-info"></i> Excused
                                    </button>
                                </div>
                            </div>
                            <div v-if="attendanceData[student.id]" class="mt-3">
                                <input
                                    v-model="attendanceData[student.id].notes"
                                    type="text"
                                    placeholder="Notes (optional)"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50 text-sm"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else-if="form.class_id" class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-users text-4xl mb-2"></i>
                    <p>No students enrolled in this class or class not found.</p>
                </div>

                <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-info-circle text-4xl mb-2"></i>
                    <p>Please select a class to mark attendance.</p>
                </div>

                <div v-if="students && students.length > 0" class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.attendances.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing || !form.class_id"
                        class="px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-save"></i>
                        {{ form.processing ? 'Saving...' : 'Save Attendance' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

