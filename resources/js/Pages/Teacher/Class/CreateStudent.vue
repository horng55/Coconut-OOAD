<script setup>
import { useForm } from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";

const title = [
    { label: "Classes", href: route("teacher.classes.index") },
    { label: "Create Student", href: "#" }
];

const props = defineProps({
    classItem: Object,
});

const form = useForm({
    name: "",
    email: "",
    phone: "",
    date_of_birth: "",
    gender: "male",
    address: "",
    student_id: "",
    admission_date: new Date().toISOString().split('T')[0],
    medical_info: "",
});

const submit = () => {
    form.post(route("teacher.classes.store-student", props.classItem.id));
};
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-plus text-white text-xl"></i>
                        </div>
                        Create New Student
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">
                        Create a new student and enroll in {{ classItem?.name }}
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                        <p class="text-sm text-blue-800 dark:text-blue-300">
                            <i class="fas fa-info-circle mr-2"></i>
                            The student will be automatically enrolled in this class. Default password will be: <strong>password123</strong>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="John Doe"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                            />
                            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                placeholder="student@example.com"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                            />
                            <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Student ID <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.student_id"
                                type="text"
                                required
                                placeholder="STU-001"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                            />
                            <div v-if="form.errors.student_id" class="text-red-500 text-sm mt-1">{{ form.errors.student_id }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone
                            </label>
                            <input
                                v-model="form.phone"
                                type="text"
                                placeholder="+1234567890"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                            />
                            <div v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Date of Birth <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.date_of_birth"
                                type="date"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                            />
                            <div v-if="form.errors.date_of_birth" class="text-red-500 text-sm mt-1">{{ form.errors.date_of_birth }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Gender <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.gender"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                            >
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <div v-if="form.errors.gender" class="text-red-500 text-sm mt-1">{{ form.errors.gender }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Admission Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.admission_date"
                                type="date"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                            />
                            <div v-if="form.errors.admission_date" class="text-red-500 text-sm mt-1">{{ form.errors.admission_date }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Address
                        </label>
                        <textarea
                            v-model="form.address"
                            rows="2"
                            placeholder="Student address..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        ></textarea>
                        <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Medical Information
                        </label>
                        <textarea
                            v-model="form.medical_info"
                            rows="3"
                            placeholder="Any medical conditions, allergies, etc..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        ></textarea>
                        <div v-if="form.errors.medical_info" class="text-red-500 text-sm mt-1">{{ form.errors.medical_info }}</div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create & Enroll Student</span>
                        </button>

                        <a
                            :href="route('teacher.classes.show', classItem?.id)"
                            class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg font-semibold transition-colors"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </TeacherLayout>
</template>
