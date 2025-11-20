<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Enrollments", href: route("admin.enrollments.index")}];

const props = defineProps({
    enrollments: Object,
    classes: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedClass = ref(props.filters?.class_id || "");
const selectedStatus = ref(props.filters?.status || "");

const handleSearch = () => {
    router.get(route('admin.enrollments.index'), {
        search: searchQuery.value,
        class_id: selectedClass.value,
        status: selectedStatus.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const deleteEnrollment = (id) => {
    if (confirm('Are you sure you want to delete this enrollment?')) {
        router.delete(route('admin.enrollments.destroy', id));
    }
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Enrollments Management"
            :subtitle="`Total: ${enrollments?.total || 0} enrollments`"
            icon="fas fa-clipboard-list"
            icon-color="text-teal-500"
            icon-bg="from-teal-500/20 to-cyan-500/20"
        >
            <template #actions>
                <div class="flex items-center gap-3 flex-wrap">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search..."
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-teal-500/50"
                    />
                    <select
                        v-model="selectedClass"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-teal-500/50"
                    >
                        <option value="">All Classes</option>
                        <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                            {{ classItem.name }} ({{ classItem.code }})
                        </option>
                    </select>
                    <select
                        v-model="selectedStatus"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-teal-500/50"
                    >
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="completed">Completed</option>
                    </select>
                    <Link
                        :href="route('admin.enrollments.create')"
                        class="bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-plus"></i>
                        Add Enrollment
                    </Link>
                </div>
            </template>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-teal-500/10 to-cyan-500/10 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Class</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Teacher</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Enrollment Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr 
                            v-for="(enrollment, index) in enrollments?.data || []" 
                            :key="enrollment.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center justify-center w-8 h-8 bg-teal-500/10 dark:bg-teal-500/30 rounded-lg text-teal-500 dark:text-teal-400 font-semibold">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ enrollment.student?.user?.full_name || 'N/A' }}</span>
                                    <span v-if="enrollment.student?.student_id" class="text-gray-600 dark:text-gray-400 text-xs block">ID: {{ enrollment.student.student_id }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ enrollment.class_model?.name || 'N/A' }}</span>
                                    <span v-if="enrollment.class_model?.code" class="text-gray-600 dark:text-gray-400 text-xs block">Code: {{ enrollment.class_model.code }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div v-if="enrollment.class_model?.teachers && enrollment.class_model.teachers.length > 0" class="space-y-1">
                                    <span v-for="teacher in enrollment.class_model.teachers" :key="teacher.id" class="block">
                                        {{ teacher.user?.full_name || 'N/A' }}
                                    </span>
                                </div>
                                <span v-else>No teachers assigned</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ enrollment.enrollment_date ? new Date(enrollment.enrollment_date).toLocaleDateString() : 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                    enrollment.status === 'active' 
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                        : enrollment.status === 'completed'
                                        ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300'
                                        : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                ]">
                                    <i :class="enrollment.status === 'active' ? 'fas fa-check-circle' : enrollment.status === 'completed' ? 'fas fa-check-double' : 'fas fa-times-circle'"></i>
                                    {{ enrollment.status || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="route('admin.enrollments.show', enrollment.id)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="View"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <Link
                                        :href="route('admin.enrollments.edit', enrollment.id)"
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button
                                        @click="deleteEnrollment(enrollment.id)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!enrollments?.data || enrollments.data.length === 0">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-clipboard-list text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                    <p>No enrollments found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="enrollments?.links" :links="enrollments.links"/>
        </AdminPageWrapper>
    </App>
</template>

