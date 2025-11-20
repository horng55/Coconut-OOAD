<script setup>
import {ref, watch} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Enrollments", href: route("admin.enrollments.index")},
    {label: "Create Enrollment", href: route("admin.enrollments.create")}
];

const props = defineProps({
    students: Array,
    classes: Array,
});

const form = useForm({
    student_id: '',
    class_id: '',
    notes: '',
});

const selectedClass = ref(null);

const updateClassInfo = () => {
    selectedClass.value = props.classes.find(c => c.id == form.class_id);
};

watch(() => form.class_id, updateClassInfo);

const submit = () => {
    form.post(route('admin.enrollments.store'), {
        onSuccess: () => {
            router.visit(route('admin.enrollments.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Create New Enrollment"
            subtitle="Enroll a student in a class"
            icon="fas fa-user-plus"
            icon-color="text-teal-500"
            icon-bg="from-teal-500/20 to-cyan-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Student <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.student_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-teal-500/50"
                        >
                            <option value="">Select Student</option>
                            <option v-for="student in students" :key="student.id" :value="student.id">
                                {{ student.name }} ({{ student.student_id }})
                            </option>
                        </select>
                        <div v-if="form.errors.student_id" class="text-red-500 text-sm mt-1">{{ form.errors.student_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Class <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.class_id"
                            @change="updateClassInfo"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-teal-500/50"
                        >
                            <option value="">Select Class</option>
                            <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                {{ classItem.name }} ({{ classItem.code }}) - Teachers: {{ classItem.teachers || 'N/A' }}
                            </option>
                        </select>
                        <div v-if="form.errors.class_id" class="text-red-500 text-sm mt-1">{{ form.errors.class_id }}</div>
                        <div v-if="selectedClass" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <p>Current Enrollments: {{ selectedClass.current_enrollments }}/{{ selectedClass.max_students }}</p>
                            <p v-if="selectedClass.current_enrollments >= selectedClass.max_students" class="text-red-500 font-medium">
                                ⚠️ Class is full!
                            </p>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            placeholder="Additional notes about this enrollment..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-teal-500/50"
                        ></textarea>
                        <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">{{ form.errors.notes }}</div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.enrollments.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-save"></i>
                        {{ form.processing ? 'Enrolling...' : 'Enroll Student' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

