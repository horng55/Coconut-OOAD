<script setup>
import {ref} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Fees", href: route("admin.fees.index")},
    {label: "Create Fee", href: route("admin.fees.create")}
];

const props = defineProps({
    classes: Array,
    defaultAcademicYear: String,
});

const form = useForm({
    name: '',
    fee_type: 'other',
    description: '',
    amount: '',
    class_id: '',
    academic_year: props.defaultAcademicYear || '',
    due_date: '',
    is_recurring: false,
    recurring_period: '',
    status: 'active',
});

const submit = () => {
    form.post(route('admin.fees.store'), {
        onSuccess: () => {
            router.visit(route('admin.fees.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Create New Fee"
            subtitle="Add a new fee to the system"
            icon="fas fa-money-bill-wave"
            icon-color="text-yellow-500"
            icon-bg="from-yellow-500/20 to-amber-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fee Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="e.g., Tuition Fee, Library Fee"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50"
                        />
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fee Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.fee_type"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50"
                        >
                            <option value="tuition">Tuition</option>
                            <option value="library">Library</option>
                            <option value="sports">Sports</option>
                            <option value="exam">Exam</option>
                            <option value="transport">Transport</option>
                            <option value="hostel">Hostel</option>
                            <option value="other">Other</option>
                        </select>
                        <div v-if="form.errors.fee_type" class="text-red-500 text-sm mt-1">{{ form.errors.fee_type }}</div>
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50"
                        />
                        <div v-if="form.errors.amount" class="text-red-500 text-sm mt-1">{{ form.errors.amount }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Class (Leave empty for all classes)
                        </label>
                        <select
                            v-model="form.class_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50"
                        >
                            <option value="">All Classes</option>
                            <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                {{ classItem.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.class_id" class="text-red-500 text-sm mt-1">{{ form.errors.class_id }}</div>
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50"
                        />
                        <div v-if="form.errors.academic_year" class="text-red-500 text-sm mt-1">{{ form.errors.academic_year }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Due Date
                        </label>
                        <input
                            v-model="form.due_date"
                            type="date"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50"
                        />
                        <div v-if="form.errors.due_date" class="text-red-500 text-sm mt-1">{{ form.errors.due_date }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.status"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 mb-2">
                            <input
                                v-model="form.is_recurring"
                                type="checkbox"
                                class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500"
                            />
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Recurring Fee</span>
                        </label>
                        <select
                            v-if="form.is_recurring"
                            v-model="form.recurring_period"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50 mt-2"
                        >
                            <option value="">Select Period</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="semester">Semester</option>
                            <option value="yearly">Yearly</option>
                        </select>
                        <div v-if="form.errors.recurring_period" class="text-red-500 text-sm mt-1">{{ form.errors.recurring_period }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            placeholder="Enter fee description (optional)"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500/50"
                        ></textarea>
                        <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.fees.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-yellow-500 to-amber-500 hover:from-yellow-600 hover:to-amber-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-save"></i>
                        {{ form.processing ? 'Creating...' : 'Create Fee' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

