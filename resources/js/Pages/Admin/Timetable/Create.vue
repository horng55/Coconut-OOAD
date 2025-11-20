<script setup>
import {ref, watch, computed} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Timetables", href: route("admin.timetables.index")},
    {label: "Create Timetable Entry", href: route("admin.timetables.create")}
];

const props = defineProps({
    classes: Array,
    allSubjects: Array,
    allTeachers: Array,
    defaultAcademicYear: String,
    selectedClassId: Number,
});

const form = useForm({
    class_id: props.selectedClassId || '',
    subject_id: '',
    teacher_id: '',
    day_of_week: 'monday',
    start_time: '',
    end_time: '',
    room: '',
    academic_year: props.defaultAcademicYear || '',
    status: 'active',
    notes: '',
});

const daysOfWeek = [
    { value: 'monday', label: 'Monday' },
    { value: 'tuesday', label: 'Tuesday' },
    { value: 'wednesday', label: 'Wednesday' },
    { value: 'thursday', label: 'Thursday' },
    { value: 'friday', label: 'Friday' },
    { value: 'saturday', label: 'Saturday' },
    { value: 'sunday', label: 'Sunday' },
];

const selectedClass = computed(() => {
    return props.classes.find(c => c.id == form.class_id);
});

const availableSubjects = computed(() => {
    if (selectedClass.value && selectedClass.value.subjects?.length > 0) {
        return selectedClass.value.subjects;
    }
    return props.allSubjects;
});

const availableTeachers = computed(() => {
    if (selectedClass.value && selectedClass.value.teachers?.length > 0) {
        return selectedClass.value.teachers;
    }
    return props.allTeachers;
});

watch(() => form.class_id, () => {
    form.subject_id = '';
    form.teacher_id = '';
});

const submit = () => {
    form.post(route('admin.timetables.store'), {
        onSuccess: () => {
            router.visit(route('admin.timetables.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Create New Timetable Entry"
            subtitle="Add a new timetable entry for a class"
            icon="fas fa-calendar-alt"
            icon-color="text-blue-500"
            icon-bg="from-blue-500/20 to-cyan-500/20"
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
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
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.subject_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        >
                            <option value="">Select Subject</option>
                            <option v-for="subject in availableSubjects" :key="subject.id" :value="subject.id">
                                {{ subject.name }} ({{ subject.code }})
                            </option>
                        </select>
                        <div v-if="form.errors.subject_id" class="text-red-500 text-sm mt-1">{{ form.errors.subject_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Teacher <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.teacher_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        >
                            <option value="">Select Teacher</option>
                            <option v-for="teacher in availableTeachers" :key="teacher.id" :value="teacher.id">
                                {{ teacher.name }} ({{ teacher.employee_id }})
                            </option>
                        </select>
                        <div v-if="form.errors.teacher_id" class="text-red-500 text-sm mt-1">{{ form.errors.teacher_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Day of Week <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.day_of_week"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        >
                            <option v-for="day in daysOfWeek" :key="day.value" :value="day.value">
                                {{ day.label }}
                            </option>
                        </select>
                        <div v-if="form.errors.day_of_week" class="text-red-500 text-sm mt-1">{{ form.errors.day_of_week }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Start Time <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.start_time"
                            type="time"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        />
                        <div v-if="form.errors.start_time" class="text-red-500 text-sm mt-1">{{ form.errors.start_time }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            End Time <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.end_time"
                            type="time"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        />
                        <div v-if="form.errors.end_time" class="text-red-500 text-sm mt-1">{{ form.errors.end_time }}</div>
                        <div v-if="form.errors.time" class="text-red-500 text-sm mt-1">{{ form.errors.time }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Room
                        </label>
                        <input
                            v-model="form.room"
                            type="text"
                            placeholder="e.g., Room 101, Lab A"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        />
                        <div v-if="form.errors.room" class="text-red-500 text-sm mt-1">{{ form.errors.room }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Academic Year <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.academic_year"
                            type="text"
                            placeholder="e.g., 2024-2025"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        />
                        <div v-if="form.errors.academic_year" class="text-red-500 text-sm mt-1">{{ form.errors.academic_year }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.status"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Notes
                    </label>
                    <textarea
                        v-model="form.notes"
                        rows="3"
                        placeholder="Additional notes about this timetable entry..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                    ></textarea>
                    <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">{{ form.errors.notes }}</div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.timetables.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white px-6 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-save"></i>
                        {{ form.processing ? 'Creating...' : 'Create Timetable Entry' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

