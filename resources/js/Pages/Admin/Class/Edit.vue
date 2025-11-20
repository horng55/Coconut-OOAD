<script setup>
import {ref} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Classes", href: route("admin.classes.index")},
    {label: "Edit Class", href: "#"}
];

const props = defineProps({
    classData: Object,
    teachers: Array,
    subjects: Array,
});

const form = useForm({
    name: props.classData?.name || '',
    code: props.classData?.code || '',
    subject_ids: props.classData?.subjects?.map(s => s.id) || [],
    teacher_ids: props.classData?.teachers?.map(t => t.id) || [],
    description: props.classData?.description || '',
    academic_year: props.classData?.academic_year || '',
    max_students: props.classData?.max_students || 30,
    status: props.classData?.status || 'active',
});

const submit = () => {
    form.put(route('admin.classes.update', props.classData.id), {
        onSuccess: () => {
            router.visit(route('admin.classes.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Edit Class"
            :subtitle="`Editing: ${classData?.name || 'Class'}`"
            icon="fas fa-edit"
            icon-color="text-amber-500"
            icon-bg="from-amber-500/20 to-orange-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Class Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="e.g., Grade 10A"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Class Code <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.code"
                            type="text"
                            required
                            placeholder="e.g., G10A"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50 font-mono"
                        />
                        <div v-if="form.errors.code" class="text-red-500 text-sm mt-1">{{ form.errors.code }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Subjects (Optional)
                        </label>
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 p-4 max-h-48 overflow-y-auto">
                            <div v-if="subjects.length === 0" class="text-gray-500 dark:text-gray-400 text-sm">
                                No subjects available
                            </div>
                            <div v-else class="space-y-2">
                                <label
                                    v-for="subject in subjects"
                                    :key="subject.id"
                                    class="flex items-center gap-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        :value="subject.id"
                                        v-model="form.subject_ids"
                                        class="w-4 h-4 text-amber-500 border-gray-300 rounded focus:ring-amber-500"
                                    />
                                    <span class="text-gray-900 dark:text-gray-100">
                                        {{ subject.name }} ({{ subject.code }})
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div v-if="form.errors.subject_ids" class="text-red-500 text-sm mt-1">{{ form.errors.subject_ids }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Teachers (Optional)
                        </label>
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 p-4 max-h-48 overflow-y-auto">
                            <div v-if="teachers.length === 0" class="text-gray-500 dark:text-gray-400 text-sm">
                                No teachers available
                            </div>
                            <div v-else class="space-y-2">
                                <label
                                    v-for="teacher in teachers"
                                    :key="teacher.id"
                                    class="flex items-center gap-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        :value="teacher.id"
                                        v-model="form.teacher_ids"
                                        class="w-4 h-4 text-amber-500 border-gray-300 rounded focus:ring-amber-500"
                                    />
                                    <span class="text-gray-900 dark:text-gray-100">
                                        {{ teacher.name }} ({{ teacher.employee_id }})
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div v-if="form.errors.teacher_ids" class="text-red-500 text-sm mt-1">{{ form.errors.teacher_ids }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Academic Year <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.academic_year"
                            type="text"
                            required
                            placeholder="e.g., 2024-2025"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.academic_year" class="text-red-500 text-sm mt-1">{{ form.errors.academic_year }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Max Students <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.max_students"
                            type="number"
                            required
                            min="1"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.max_students" class="text-red-500 text-sm mt-1">{{ form.errors.max_students }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.status"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="completed">Completed</option>
                        </select>
                        <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            placeholder="Enter class description (optional)"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        ></textarea>
                        <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.classes.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-save"></i>
                        {{ form.processing ? 'Updating...' : 'Update Class' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

