<script setup>
import {ref} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Students", href: route("admin.students.index")},
    {label: "Create Student", href: route("admin.students.create")}
];

const props = defineProps({
    classes: Array,
    teachers: Array,
});

const form = useForm({
    first_name: '',
    last_name: '',
    username: '',
    email: '',
    password: '',
    student_id: '',
    admission_date: '',
    gender: '',
    phone_number: '',
    medical_info: '',
    class_ids: [],
    teacher_ids: [],
});

const submit = () => {
    console.log('Form data:', form.data());
    console.log('Form errors before submit:', form.errors);
    form.post(route('admin.students.store'), {
        onSuccess: () => {
            console.log('Student created successfully');
            router.visit(route('admin.students.index'));
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
            console.error('All form errors:', form.errors);
            // Scroll to top to see error message
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Create New Student"
            subtitle="Add a new student to the system"
            icon="fas fa-user-plus"
            icon-color="text-emerald-500"
            icon-bg="from-emerald-500/20 to-teal-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <!-- Display general errors -->
                <div v-if="form.errors && Object.keys(form.errors).length > 0" class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <h4 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">
                        <i class="fas fa-exclamation-circle mr-2"></i>Please fix the following errors:
                    </h4>
                    <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                        <li v-for="(error, key) in form.errors" :key="key">
                            <strong>{{ key }}:</strong> {{ error }}
                        </li>
                    </ul>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.first_name"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                        <div v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">{{ form.errors.first_name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.last_name"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                        <div v-if="form.errors.last_name" class="text-red-500 text-sm mt-1">{{ form.errors.last_name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.username"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                        <div v-if="form.errors.username" class="text-red-500 text-sm mt-1">{{ form.errors.username }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                        <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            minlength="8"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                        <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Student ID <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.student_id"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                        <div v-if="form.errors.student_id" class="text-red-500 text-sm mt-1">{{ form.errors.student_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Admission Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.admission_date"
                            type="date"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                        <div v-if="form.errors.admission_date" class="text-red-500 text-sm mt-1">{{ form.errors.admission_date }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Gender
                        </label>
                        <select
                            v-model="form.gender"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        >
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <div v-if="form.errors.gender" class="text-red-500 text-sm mt-1">{{ form.errors.gender }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Phone Number
                        </label>
                        <input
                            v-model="form.phone_number"
                            type="tel"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                        <div v-if="form.errors.phone_number" class="text-red-500 text-sm mt-1">{{ form.errors.phone_number }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Medical Information
                        </label>
                        <textarea
                            v-model="form.medical_info"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        ></textarea>
                        <div v-if="form.errors.medical_info" class="text-red-500 text-sm mt-1">{{ form.errors.medical_info }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-chalkboard-teacher text-emerald-500 mr-2"></i>
                            Assign to Classes (Optional)
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-64 overflow-y-auto p-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                            <label
                                v-for="classItem in classes"
                                :key="classItem.id"
                                class="flex items-start gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-white dark:hover:bg-gray-800 transition-colors"
                                :class="{ 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-300 dark:border-emerald-700': form.class_ids.includes(classItem.id) }"
                            >
                                <input
                                    v-model="form.class_ids"
                                    type="checkbox"
                                    :value="classItem.id"
                                    class="mt-1 w-4 h-4 text-emerald-500 rounded focus:ring-2 focus:ring-emerald-500/50"
                                />
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ classItem.name }}
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ classItem.code }} • {{ classItem.academic_year }}
                                    </p>
                                </div>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <i class="fas fa-info-circle"></i> Select the classes this student should be enrolled in
                        </p>
                        <div v-if="form.errors.class_ids" class="text-red-500 text-sm mt-1">{{ form.errors.class_ids }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-chalkboard-teacher text-blue-500 mr-2"></i>
                            Assign to Teachers (Optional)
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-64 overflow-y-auto p-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                            <label
                                v-for="teacher in teachers"
                                :key="teacher.id"
                                class="flex items-start gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-white dark:hover:bg-gray-800 transition-colors"
                                :class="{ 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700': form.teacher_ids.includes(teacher.id) }"
                            >
                                <input
                                    v-model="form.teacher_ids"
                                    type="checkbox"
                                    :value="teacher.id"
                                    class="mt-1 w-4 h-4 text-blue-500 rounded focus:ring-2 focus:ring-blue-500/50"
                                />
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ teacher.name }}
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ teacher.employee_id }} • {{ teacher.subject_specialization }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">
                                        {{ teacher.email }}
                                    </p>
                                </div>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <i class="fas fa-info-circle"></i> Assign teachers to this student (student will be enrolled in teacher's classes)
                        </p>
                        <div v-if="form.errors.teacher_ids" class="text-red-500 text-sm mt-1">{{ form.errors.teacher_ids }}</div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.students.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-save"></i>
                        {{ form.processing ? 'Creating...' : 'Create Student' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

