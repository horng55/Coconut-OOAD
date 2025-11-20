<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Students", href: route("admin.students.index")}];

const props = defineProps({
    students: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");

const handleSearch = () => {
    router.get(route('admin.students.index'), { search: searchQuery.value }, {
        preserveState: true,
        preserveScroll: true
    });
};

const deleteStudent = (id) => {
    if (confirm('Are you sure you want to delete this student?')) {
        router.delete(route('admin.students.destroy', id));
    }
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Students Management"
            :subtitle="`Total: ${students?.total || 0} students`"
            icon="fas fa-user-graduate"
            icon-color="text-emerald-500"
            icon-bg="from-emerald-500/20 to-teal-500/20"
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search students..."
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                    />
                    <Link
                        :href="route('admin.students.create')"
                        class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-plus"></i>
                        Add Student
                    </Link>
                </div>
            </template>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-emerald-500/10 to-teal-500/10 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Email</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Parent</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr 
                            v-for="(student, index) in students?.data || []" 
                            :key="student.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center justify-center w-8 h-8 bg-emerald-500/10 dark:bg-emerald-500/30 rounded-lg text-emerald-500 dark:text-emerald-400 font-semibold">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ student.user?.full_name || 'N/A' }}</span>
                                    <span v-if="student.user?.username" class="text-gray-600 dark:text-gray-400 text-xs block">@{{ student.user.username }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ student.user?.email || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ student.student_id || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ student.parent?.user?.full_name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                    student.status === 'active' 
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                        : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                ]">
                                    <i :class="student.status === 'active' ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                                    {{ student.status || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="route('admin.students.show', student.id)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="View"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <Link
                                        :href="route('admin.students.edit', student.id)"
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button
                                        @click="deleteStudent(student.id)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!students?.data || students.data.length === 0">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-user-graduate text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                    <p>No students found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="students?.links" :links="students.links"/>
        </AdminPageWrapper>
    </App>
</template>

