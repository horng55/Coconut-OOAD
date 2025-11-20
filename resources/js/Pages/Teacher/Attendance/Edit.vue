<script setup>
import {router, useForm, Link} from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";

const title = [
    {label: "Attendance", href: route("teacher.attendances.index")},
    {label: "Edit Attendance", href: "#"}
];

const props = defineProps({
    attendance: Object,
});

const form = useForm({
    status: props.attendance?.status || 'present',
    notes: props.attendance?.notes || '',
});

const submit = () => {
    form.put(route('teacher.attendances.update', props.attendance.id), {
        onSuccess: () => {
            router.visit(route('teacher.attendances.index'));
        }
    });
};
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-6">
                    <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                        <i class="fas fa-edit"></i>
                        Edit Attendance
                    </h1>
                    <p class="text-purple-100 mt-1">Editing attendance for {{ attendance?.student?.user?.full_name || 'Student' }}</p>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Student</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ attendance?.student?.user?.full_name || 'N/A' }}</p>
                            <p v-if="attendance?.student?.student_id" class="text-sm text-gray-600 dark:text-gray-400">ID: {{ attendance.student.student_id }}</p>
                        </div>

                        <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Class</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ attendance?.class_model?.name || 'N/A' }}</p>
                            <p v-if="attendance?.class_model?.code" class="text-sm text-gray-600 dark:text-gray-400">Code: {{ attendance.class_model.code }}</p>
                        </div>

                        <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Date</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ attendance?.date ? new Date(attendance.date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A' }}
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <button
                                    type="button"
                                    @click="form.status = 'present'"
                                    :class="[
                                        'px-4 py-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 font-medium',
                                        form.status === 'present'
                                            ? 'bg-green-500 text-white shadow-lg'
                                            : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-green-400'
                                    ]"
                                >
                                    <i class="fas fa-check-circle"></i>
                                    Present
                                </button>
                                <button
                                    type="button"
                                    @click="form.status = 'absent'"
                                    :class="[
                                        'px-4 py-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 font-medium',
                                        form.status === 'absent'
                                            ? 'bg-red-500 text-white shadow-lg'
                                            : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-red-400'
                                    ]"
                                >
                                    <i class="fas fa-times-circle"></i>
                                    Absent
                                </button>
                                <button
                                    type="button"
                                    @click="form.status = 'late'"
                                    :class="[
                                        'px-4 py-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 font-medium',
                                        form.status === 'late'
                                            ? 'bg-yellow-500 text-white shadow-lg'
                                            : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-yellow-400'
                                    ]"
                                >
                                    <i class="fas fa-clock"></i>
                                    Late
                                </button>
                                <button
                                    type="button"
                                    @click="form.status = 'excused'"
                                    :class="[
                                        'px-4 py-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 font-medium',
                                        form.status === 'excused'
                                            ? 'bg-blue-500 text-white shadow-lg'
                                            : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-blue-400'
                                    ]"
                                >
                                    <i class="fas fa-info-circle"></i>
                                    Excused
                                </button>
                            </div>
                            <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Notes
                            </label>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                placeholder="Additional notes (optional)..."
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                            ></textarea>
                            <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">{{ form.errors.notes }}</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <Link
                            :href="route('teacher.attendances.index')"
                            class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                            <i v-else class="fas fa-save"></i>
                            {{ form.processing ? 'Updating...' : 'Update Attendance' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </TeacherLayout>
</template>

