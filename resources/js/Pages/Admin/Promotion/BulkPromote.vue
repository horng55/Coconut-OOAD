<script setup>
import {ref, watch, computed} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Promotions", href: route("admin.promotions.index")},
    {label: "Bulk Promote", href: route("admin.promotions.bulk-promote")}
];

const props = defineProps({
    classes: Array,
    students: Array,
    defaultAcademicYear: String,
    selectedFromClassId: Number,
});

const form = useForm({
    from_class_id: props.selectedFromClassId || '',
    to_class_id: '',
    from_academic_year: props.defaultAcademicYear || '',
    to_academic_year: '',
    promotion_date: new Date().toISOString().split('T')[0],
    promotion_type: 'automatic',
    student_ids: [],
    notes: '',
});

const promotionTypes = [
    { value: 'automatic', label: 'Automatic' },
    { value: 'manual', label: 'Manual' },
    { value: 'conditional', label: 'Conditional' },
    { value: 'repeated', label: 'Repeated' },
];

const selectedStudents = computed(() => {
    return props.students.filter(s => form.student_ids.includes(s.id));
});

watch(() => form.from_class_id, () => {
    form.student_ids = [];
    const fromClass = props.classes.find(c => c.id == form.from_class_id);
    if (fromClass) {
        form.from_academic_year = fromClass.academic_year || form.from_academic_year;
    }
    // Reload page to get students for selected class
    if (form.from_class_id) {
        router.get(route('admin.promotions.bulk-promote', { from_class_id: form.from_class_id }), {}, {
            preserveState: true,
            preserveScroll: true
        });
    }
});

const toggleStudent = (studentId) => {
    const index = form.student_ids.indexOf(studentId);
    if (index > -1) {
        form.student_ids.splice(index, 1);
    } else {
        form.student_ids.push(studentId);
    }
};

const selectAll = () => {
    form.student_ids = props.students.map(s => s.id);
};

const deselectAll = () => {
    form.student_ids = [];
};

const submit = () => {
    if (form.student_ids.length === 0) {
        alert('Please select at least one student to promote.');
        return;
    }

    form.post(route('admin.promotions.bulk-promote.store'), {
        onSuccess: () => {
            router.visit(route('admin.promotions.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Bulk Promote Students"
            subtitle="Promote multiple students from one class to another"
            icon="fas fa-users"
            icon-color="text-blue-500"
            icon-bg="from-blue-500/20 to-cyan-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            From Class <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.from_class_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        >
                            <option v-for="type in promotionTypes" :key="type.value" :value="type.value">
                                {{ type.label }}
                            </option>
                        </select>
                        <div v-if="form.errors.promotion_type" class="text-red-500 text-sm mt-1">{{ form.errors.promotion_type }}</div>
                    </div>
                </div>

                <div v-if="form.from_class_id && students.length > 0">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Select Students to Promote <span class="text-red-500">*</span>
                            <span class="text-gray-500 text-xs ml-2">({{ form.student_ids.length }} selected)</span>
                        </label>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                @click="selectAll"
                                class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors"
                            >
                                Select All
                            </button>
                            <button
                                type="button"
                                @click="deselectAll"
                                class="px-3 py-1 text-sm bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors"
                            >
                                Deselect All
                            </button>
                        </div>
                    </div>
                    <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 max-h-96 overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            <label
                                v-for="student in students"
                                :key="student.id"
                                class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                :class="form.student_ids.includes(student.id) ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-600' : ''"
                            >
                                <input
                                    type="checkbox"
                                    :checked="form.student_ids.includes(student.id)"
                                    @change="toggleStudent(student.id)"
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                                />
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ student.name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ student.student_id }}</div>
                                </div>
                            </label>
                        </div>
                        <div v-if="students.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-8">
                            No students found in the selected class.
                        </div>
                    </div>
                    <div v-if="form.errors.student_ids" class="text-red-500 text-sm mt-1">{{ form.errors.student_ids }}</div>
                    <div v-if="form.errors.bulk" class="text-red-500 text-sm mt-2">
                        <ul class="list-disc list-inside">
                            <li v-for="(error, index) in form.errors.bulk" :key="index">{{ error }}</li>
                        </ul>
                    </div>
                </div>

                <div v-else-if="form.from_class_id && students.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-8">
                    No students found in the selected class.
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Notes
                    </label>
                    <textarea
                        v-model="form.notes"
                        rows="3"
                        placeholder="Additional notes about this bulk promotion..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
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
                        :disabled="form.processing || form.student_ids.length === 0"
                        class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white px-6 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-users"></i>
                        {{ form.processing ? 'Promoting...' : `Promote ${form.student_ids.length} Student(s)` }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

