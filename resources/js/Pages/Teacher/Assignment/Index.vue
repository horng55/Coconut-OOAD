<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Assignments", href: route("teacher.assignments.index")}];

const props = defineProps({
    assignments: Object,
    classes: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedClassId = ref(props.filters?.class_id || "");
const selectedStatus = ref(props.filters?.status || "");

const handleSearch = () => {
    router.get(route('teacher.assignments.index'), {
        search: searchQuery.value,
        class_id: selectedClassId.value,
        status: selectedStatus.value,
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const handleDelete = (id) => {
    if (confirm('Are you sure you want to delete this assignment? All submissions will also be deleted.')) {
        router.delete(route('teacher.assignments.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                alert('Assignment deleted successfully');
            },
        });
    }
};

const getStatusColor = (status) => {
    const colors = {
        'draft': 'bg-gray-500',
        'published': 'bg-green-500',
        'closed': 'bg-red-500',
    };
    return colors[status] || 'bg-gray-500';
};

const isOverdue = (assignment) => {
    const now = new Date();
    const dueDate = new Date(assignment.due_date + ' ' + (assignment.due_time || '23:59:59'));
    return now > dueDate && assignment.status === 'published';
};
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-file-alt text-white text-xl"></i>
                            </div>
                            Assignments
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Manage your class assignments</p>
                    </div>
                    <Link
                        :href="route('teacher.assignments.create')"
                        class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2"
                    >
                        <i class="fas fa-plus"></i>
                        Create Assignment
                    </Link>
                </div>

                <!-- Filters -->
                <div class="flex flex-col md:flex-row items-start md:items-center gap-3 mb-6">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search assignments..."
                        class="flex-1 md:flex-none px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                    />
                    <select
                        v-model="selectedClassId"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                    >
                        <option value="">All Classes</option>
                        <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                            {{ cls.name }} ({{ cls.code }})
                        </option>
                    </select>
                    <select
                        v-model="selectedStatus"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                    >
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="closed">Closed</option>
                    </select>
                    <button
                        @click="handleSearch"
                        class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-colors"
                    >
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <!-- Assignments List -->
                <div v-if="assignments?.data && assignments.data.length > 0" class="space-y-4">
                    <div
                        v-for="assignment in assignments.data"
                        :key="assignment.id"
                        class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 border border-purple-200 dark:border-purple-800 hover:shadow-lg transition-all duration-200"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                        {{ assignment.title }}
                                    </h3>
                                    <span :class="['px-3 py-1 text-white text-xs font-semibold rounded-full', getStatusColor(assignment.status)]">
                                        {{ assignment.status }}
                                    </span>
                                    <span v-if="isOverdue(assignment)" class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">
                                        Overdue
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    {{ assignment.class_model?.name }} ({{ assignment.class_model?.code }})
                                </p>
                                <p v-if="assignment.description" class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                                    {{ assignment.description }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Due Date:</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ new Date(assignment.due_date).toLocaleDateString() }}
                                    <span v-if="assignment.due_time" class="text-gray-600 dark:text-gray-400">
                                        at {{ assignment.due_time }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Max Score:</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ assignment.max_score }} points
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Submissions:</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ assignment.submissions?.length || 0 }} submitted
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <Link
                                :href="route('teacher.assignments.show', assignment.id)"
                                class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-colors flex items-center gap-2"
                            >
                                <i class="fas fa-eye"></i>
                                View Details
                            </Link>
                            <Link
                                :href="route('teacher.assignments.edit', assignment.id)"
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors flex items-center gap-2"
                            >
                                <i class="fas fa-edit"></i>
                                Edit
                            </Link>
                            <button
                                @click="handleDelete(assignment.id)"
                                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors flex items-center gap-2"
                            >
                                <i class="fas fa-trash"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No Assignments Found</p>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Create your first assignment to get started.</p>
                    <Link
                        :href="route('teacher.assignments.create')"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg"
                    >
                        <i class="fas fa-plus"></i>
                        Create Assignment
                    </Link>
                </div>

                <div v-if="assignments?.data && assignments.data.length > 0" class="mt-6">
                    <Pagination :links="assignments.links" />
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>
