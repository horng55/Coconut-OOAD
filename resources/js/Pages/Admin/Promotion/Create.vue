<script setup>
import {ref, watch, computed} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Promotions", href: route("admin.promotions.index")},
    {label: "Create Promotion", href: route("admin.promotions.create")}
];

const props = defineProps({
    classes: Array,
    students: Array,
    defaultAcademicYear: String,
    selectedStudentId: Number,
});

const form = useForm({
    student_id: props.selectedStudentId || '',
    from_class_id: '',
    to_class_id: '',
    from_academic_year: props.defaultAcademicYear || '',
    to_academic_year: '',
    promotion_date: new Date().toISOString().split('T')[0],
    promotion_type: 'manual',
    status: 'pending',
    notes: '',
});

const promotionTypes = [
    { value: 'automatic', label: 'Automatic' },
    { value: 'manual', label: 'Manual' },
    { value: 'conditional', label: 'Conditional' },
    { value: 'repeated', label: 'Repeated' },
];

const statuses = [
    { value: 'pending', label: 'Pending' },
    { value: 'approved', label: 'Approved' },
    { value: 'rejected', label: 'Rejected' },
    { value: 'completed', label: 'Completed' },
];

const availableFromClasses = computed(() => {
    if (!form.student_id) return [];
    // Filter classes where student is enrolled
    return props.classes.filter(c => {
        // This would ideally check enrollment, but for now show all
        return true;
    });
});

watch(() => form.from_class_id, () => {
    const fromClass = props.classes.find(c => c.id == form.from_class_id);
    if (fromClass) {
        form.from_academic_year = fromClass.academic_year || form.from_academic_year;
    }
});

const submit = () => {
    form.post(route('admin.promotions.store'), {
        onSuccess: () => {
            router.visit(route('admin.promotions.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Create New Promotion"
            subtitle="Promote a student from one class to another"
            icon="fas fa-graduation-cap"
            icon-color="text-green-500"
            icon-bg="from-green-500/20 to-emerald-500/20"
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
                            From Class <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.from_class_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        >
                            <option value="">Select Source Class</option>
                            <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                {{ classItem.name }} ({{ classItem.code }}) - {{ classItem.academic_year }}
                            </option>
                        </select>
                        <div v-if="form.errors.from_class_id" class="text-red-500 text-sm mt-1">{{ form.errors.from_class_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            To Class <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.to_class_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        >
                            <option value="">Select Target Class</option>
                            <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                {{ classItem.name }} ({{ classItem.code }}) - {{ classItem.academic_year }}
                            </option>
                        </select>
                        <div v-if="form.errors.to_class_id" class="text-red-500 text-sm mt-1">{{ form.errors.to_class_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            From Academic Year <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.from_academic_year"
                            type="text"
                            placeholder="e.g., 2023-2024"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        />
                        <div v-if="form.errors.from_academic_year" class="text-red-500 text-sm mt-1">{{ form.errors.from_academic_year }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            To Academic Year <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.to_academic_year"
                            type="text"
                            placeholder="e.g., 2024-2025"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        />
                        <div v-if="form.errors.to_academic_year" class="text-red-500 text-sm mt-1">{{ form.errors.to_academic_year }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Promotion Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.promotion_date"
                            type="date"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        />
                        <div v-if="form.errors.promotion_date" class="text-red-500 text-sm mt-1">{{ form.errors.promotion_date }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Promotion Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.promotion_type"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        >
                            <option v-for="type in promotionTypes" :key="type.value" :value="type.value">
                                {{ type.label }}
                            </option>
                        </select>
                        <div v-if="form.errors.promotion_type" class="text-red-500 text-sm mt-1">{{ form.errors.promotion_type }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.status"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        >
                            <option v-for="status in statuses" :key="status.value" :value="status.value">
                                {{ status.label }}
                            </option>
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
                        placeholder="Additional notes about this promotion..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                    ></textarea>
                    <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">{{ form.errors.notes }}</div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.promotions.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-6 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-save"></i>
                        {{ form.processing ? 'Creating...' : 'Create Promotion' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

