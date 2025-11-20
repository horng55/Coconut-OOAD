<script setup>
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Enrollments", href: route("admin.enrollments.index")},
    {label: "Edit Enrollment", href: "#"}
];

const props = defineProps({
    enrollment: Object,
});

const form = useForm({
    status: props.enrollment?.status || 'active',
    notes: props.enrollment?.notes || '',
});

const submit = () => {
    form.put(route('admin.enrollments.update', props.enrollment.id), {
        onSuccess: () => {
            router.visit(route('admin.enrollments.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Edit Enrollment"
            :subtitle="`Editing enrollment for ${enrollment?.student?.user?.full_name || enrollment?.student?.user?.first_name + ' ' + enrollment?.student?.user?.last_name || 'Student'} in ${enrollment?.class_model?.name || 'Class'}`"
            icon="fas fa-edit"
            icon-color="text-amber-500"
            icon-bg="from-amber-500/20 to-orange-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6 border border-blue-200 dark:border-blue-800">
                    <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2">Enrollment Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-blue-700 dark:text-blue-400">Student:</span>
                            <span class="ml-2 font-medium text-blue-900 dark:text-blue-200">
                                {{ enrollment?.student?.user?.full_name || enrollment?.student?.user?.first_name + ' ' + enrollment?.student?.user?.last_name || 'N/A' }}
                                ({{ enrollment?.student?.student_id || 'N/A' }})
                            </span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-400">Class:</span>
                            <span class="ml-2 font-medium text-blue-900 dark:text-blue-200">
                                {{ enrollment?.class_model?.name || 'N/A' }} ({{ enrollment?.class_model?.code || 'N/A' }})
                            </span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-400">Enrollment Date:</span>
                            <span class="ml-2 font-medium text-blue-900 dark:text-blue-200">
                                {{ enrollment?.enrollment_date ? new Date(enrollment.enrollment_date).toLocaleDateString() : 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            placeholder="Additional notes about this enrollment..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
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
                        class="px-6 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-save"></i>
                        {{ form.processing ? 'Updating...' : 'Update Enrollment' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

