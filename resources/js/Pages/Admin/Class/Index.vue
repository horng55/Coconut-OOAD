<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Classes", href: route("admin.classes.index")}];

const props = defineProps({
    classes: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const academicYear = ref(props.filters?.academic_year || "");

const handleSearch = () => {
    router.get(route('admin.classes.index'), {
        search: searchQuery.value,
        academic_year: academicYear.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const deleteClass = (id) => {
    if (confirm('Are you sure you want to delete this class?')) {
        router.delete(route('admin.classes.destroy', id));
    }
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Classes Management"
            :subtitle="`Total: ${classes?.total || 0} classes`"
            icon="fas fa-book-reader"
            icon-color="text-amber-500"
            icon-bg="from-amber-500/20 to-orange-500/20"
        >
            <template #actions>
                <div class="flex items-center gap-3 flex-wrap">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search classes..."
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                    />
                    <input
                        v-model="academicYear"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Academic Year (e.g., 2024-2025)"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                    />
                    <Link
                        :href="route('admin.classes.create')"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-plus"></i>
                        Add Class
                    </Link>
                </div>
            </template>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Class Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Code</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Subject</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Teacher</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Academic Year</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Max Students</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr 
                            v-for="(classItem, index) in classes?.data || []" 
                            :key="classItem.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center justify-center w-8 h-8 bg-amber-500/10 dark:bg-amber-500/30 rounded-lg text-amber-500 dark:text-amber-400 font-semibold">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ classItem.name || 'N/A' }}</span>
                                    <span v-if="classItem.description" class="text-gray-600 dark:text-gray-400 text-xs block truncate max-w-xs">{{ classItem.description }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ classItem.code || 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div v-if="classItem.subjects && classItem.subjects.length > 0" class="space-y-1">
                                    <span v-for="subject in classItem.subjects" :key="subject.id" class="block">
                                        {{ subject.name || 'N/A' }}
                                    </span>
                                </div>
                                <span v-else>No subjects assigned</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div v-if="classItem.teachers && classItem.teachers.length > 0" class="space-y-1">
                                    <span v-for="teacher in classItem.teachers" :key="teacher.id" class="block">
                                        {{ teacher.user?.full_name || 'N/A' }}
                                    </span>
                                </div>
                                <span v-else>No teachers assigned</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ classItem.academic_year || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ classItem.max_students || 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                    classItem.status === 'active' 
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                        : classItem.status === 'completed'
                                        ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300'
                                        : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                ]">
                                    <i :class="classItem.status === 'active' ? 'fas fa-check-circle' : classItem.status === 'completed' ? 'fas fa-check-double' : 'fas fa-times-circle'"></i>
                                    {{ classItem.status || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="route('admin.classes.show', classItem.id)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="View"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <Link
                                        :href="route('admin.classes.edit', classItem.id)"
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button
                                        @click="deleteClass(classItem.id)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!classes?.data || classes.data.length === 0">
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-book-reader text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                    <p>No classes found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="classes?.links" :links="classes.links"/>
        </AdminPageWrapper>
    </App>
</template>

