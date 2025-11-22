<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import StudentLayout from "../../../Layouts/StudentLayout.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Assignments", href: route("student.assignments.index")}];

const props = defineProps({
    assignments: Object,
    classes: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedClassId = ref(props.filters?.class_id || "");
const selectedStatusFilter = ref(props.filters?.status_filter || "");

const handleSearch = () => {
    router.get(route('student.assignments.index'), {
        search: searchQuery.value,
        class_id: selectedClassId.value,
        status_filter: selectedStatusFilter.value,
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const getSubmissionStatus = (assignment) => {
    if (assignment.submissions && assignment.submissions.length > 0) {
        const submission = assignment.submissions[0];
        if (submission.status === 'graded') {
            return { text: 'Graded', color: 'bg-green-500' };
        }
        return { text: 'Submitted', color: 'bg-blue-500' };
    }
    return { text: 'Not Submitted', color: 'bg-gray-500' };
};

const isOverdue = (assignment) => {
    const now = new Date();
    const dueDate = new Date(assignment.due_date + ' ' + (assignment.due_time || '23:59:59'));
    return now > dueDate && !assignment.submissions?.length;
};
</script>

<template>
    <StudentLayout :title="title">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-file-alt text-white text-xl"></i>
                            </div>
                            My Assignments
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">View and submit your class assignments</p>
                    </div>
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
                        v-model="selectedStatusFilter"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                    >
                        <option value="">All Status</option>
                        <option value="pending">Not Submitted</option>
                        <option value="submitted">Submitted</option>
                        <option value="graded">Graded</option>
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
                                    <span :class="['px-3 py-1 text-white text-xs font-semibold rounded-full', getSubmissionStatus(assignment).color]">
                                        {{ getSubmissionStatus(assignment).text }}
                                    </span>
                                    <span v-if="isOverdue(assignment)" class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">
                                        Overdue
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    {{ assignment.class_model?.name }} ({{ assignment.class_model?.code }})
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Teacher: {{ assignment.teacher?.user?.name }}
                                </p>
                                <p v-if="assignment.description" class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2 mt-2">
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
                            <div v-if="assignment.submissions?.length && assignment.submissions[0].score !== null">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Your Score:</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ assignment.submissions[0].score }} / {{ assignment.max_score }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <Link
                                :href="route('student.assignments.show', assignment.id)"
                                class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-colors flex items-center gap-2"
                            >
                                <i class="fas fa-eye"></i>
                                View Details
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No Assignments Found</p>
                    <p class="text-gray-500 dark:text-gray-400">You don't have any assignments yet.</p>
                </div>

                <div v-if="assignments?.data && assignments.data.length > 0" class="mt-6">
                    <Pagination :links="assignments.links" />
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
