<script setup>
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Fees", href: route("admin.fees.index")},
    {label: "Create Payment Record", href: "#"}
];

const props = defineProps({
    fee: Object,
    students: Array,
});

const form = useForm({
    student_id: '',
    amount: props.fee?.amount || '',
    due_date: '',
    academic_year: props.fee?.academic_year || '',
    notes: '',
});

const submit = () => {
    form.post(route('admin.fees.payments.store', props.fee.id), {
        onSuccess: () => {
            router.visit(route('admin.fees.show', props.fee.id));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Create Payment Record"
            :subtitle="`For: ${fee?.name || 'Fee'}`"
            icon="fas fa-credit-card"
            icon-color="text-green-500"
            icon-bg="from-green-500/20 to-emerald-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Fee Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Fee:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ fee?.name || 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Type:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white capitalize">{{ fee?.fee_type || 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white">${{ fee?.amount || '0.00' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Class:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ fee?.class_model?.name || 'All Classes' }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Student <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.student_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
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
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            min="0"
                            required
                            placeholder="0.00"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        />
                        <div v-if="form.errors.amount" class="text-red-500 text-sm mt-1">{{ form.errors.amount }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Due Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.due_date"
                            type="date"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        />
                        <div v-if="form.errors.due_date" class="text-red-500 text-sm mt-1">{{ form.errors.due_date }}</div>
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        />
                        <div v-if="form.errors.academic_year" class="text-red-500 text-sm mt-1">{{ form.errors.academic_year }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            placeholder="Optional notes about this payment record"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        ></textarea>
                        <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">{{ form.errors.notes }}</div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.fees.show', fee?.id)"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-save"></i>
                        {{ form.processing ? 'Creating...' : 'Create Payment Record' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

