<script setup>
import {ref, watch} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "ID Cards", href: route("admin.id-cards.index")},
    {label: "Edit ID Card", href: "#"}
];

const props = defineProps({
    idCard: Object,
    students: Array,
    teachers: Array,
});

const form = useForm({
    card_type: props.idCard?.card_type || 'student',
    student_id: props.idCard?.student_id || '',
    teacher_id: props.idCard?.teacher_id || '',
    card_number: props.idCard?.card_number || '',
    issue_date: props.idCard?.issue_date || '',
    expiry_date: props.idCard?.expiry_date || '',
    status: props.idCard?.status || 'active',
    photo: null,
    notes: props.idCard?.notes || '',
});

// Reset owner selection when card type changes
watch(() => form.card_type, () => {
    if (form.card_type === 'student') {
        form.teacher_id = '';
    } else {
        form.student_id = '';
    }
});

const submit = () => {
    form.put(route('admin.id-cards.update', props.idCard.id), {
        forceFormData: true,
        onSuccess: () => {
            router.visit(route('admin.id-cards.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            :title="`Edit ID Card: ${idCard?.card_number || 'N/A'}`"
            subtitle="Update ID card information"
            icon="fas fa-edit"
            icon-color="text-amber-500"
            icon-bg="from-amber-500/20 to-orange-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Card Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.card_type"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        >
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                        </select>
                        <div v-if="form.errors.card_type" class="text-red-500 text-sm mt-1">{{ form.errors.card_type }}</div>
                    </div>

                    <div v-if="form.card_type === 'student'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Student <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.student_id"
                            :required="form.card_type === 'student'"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        >
                            <option value="">Select Student</option>
                            <option v-for="student in students" :key="student.id" :value="student.id">
                                {{ student.name }} ({{ student.student_id }})
                            </option>
                        </select>
                        <div v-if="form.errors.student_id" class="text-red-500 text-sm mt-1">{{ form.errors.student_id }}</div>
                    </div>

                    <div v-if="form.card_type === 'teacher'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Teacher <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.teacher_id"
                            :required="form.card_type === 'teacher'"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        >
                            <option value="">Select Teacher</option>
                            <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                {{ teacher.name }} ({{ teacher.employee_id }})
                            </option>
                        </select>
                        <div v-if="form.errors.teacher_id" class="text-red-500 text-sm mt-1">{{ form.errors.teacher_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Card Number <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.card_number"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.card_number" class="text-red-500 text-sm mt-1">{{ form.errors.card_number }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Issue Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.issue_date"
                            type="date"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.issue_date" class="text-red-500 text-sm mt-1">{{ form.errors.issue_date }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Expiry Date
                        </label>
                        <input
                            v-model="form.expiry_date"
                            type="date"
                            :min="form.issue_date"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.expiry_date" class="text-red-500 text-sm mt-1">{{ form.errors.expiry_date }}</div>
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
                            <option value="expired">Expired</option>
                            <option value="revoked">Revoked</option>
                        </select>
                        <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Photo
                        </label>
                        <div v-if="idCard?.photo_path" class="mb-2">
                            <img :src="`/storage/${idCard.photo_path}`" alt="Current photo" class="w-24 h-24 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Current photo</p>
                        </div>
                        <input
                            @input="form.photo = $event.target.files[0]"
                            type="file"
                            accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.photo" class="text-red-500 text-sm mt-1">{{ form.errors.photo }}</div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave empty to keep current photo. Max size: 2MB</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Notes
                    </label>
                    <textarea
                        v-model="form.notes"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                    ></textarea>
                    <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">{{ form.errors.notes }}</div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.id-cards.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">Updating...</span>
                        <span v-else>Update ID Card</span>
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

