<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Assessments", href: route("teacher.assessments.index")}];

const props = defineProps({
    assessments: Object,
    classes: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedClass = ref(props.filters?.class_id || "");
const selectedType = ref(props.filters?.assessment_type || "");
const selectedStatus = ref(props.filters?.status || "");

const handleSearch = () => {
    router.get(route('teacher.assessments.index'), {
        search: searchQuery.value,
        class_id: selectedClass.value,
        assessment_type: selectedType.value,
        status: selectedStatus.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const deleteAssessment = (id) => {
    if (confirm('Are you sure you want to delete this assessment?')) {
        router.delete(route('teacher.assessments.destroy', id));
    }
};
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 p-6">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                                <i class="fas fa-clipboard-list"></i>
                                Assessments Management
                            </h1>
                            <p class="text-indigo-100 mt-1">Total: {{ assessments?.total || 0 }} assessment records</p>
                        </div>
                        <Link
                            :href="route('teacher.assessments.create')"
                            class="bg-white text-indigo-600 hover:bg-indigo-50 px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 font-medium shadow-lg hover:shadow-xl"
                        >
                            <i class="fas fa-plus"></i>
                            Create Assessment
                        </Link>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex items-center gap-3 flex-wrap mb-6">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Search assessment or class..."
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        />
                        <select
                            v-model="selectedClass"
                            @change="handleSearch"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        >
                            <option value="">All Classes</option>
                            <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                {{ classItem.name }} ({{ classItem.code }})
                            </option>
                        </select>
                        <select
                            v-model="selectedType"
                            @change="handleSearch"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        >
                            <option value="">All Types</option>
                            <option value="quiz">Quiz</option>
                            <option value="assignment">Assignment</option>
                            <option value="exam">Exam</option>
                            <option value="mid_term">Mid-Term</option>
                            <option value="project">Project</option>
                            <option value="participation">Participation</option>
                        </select>
                        <select
                            v-model="selectedStatus"
                            @change="handleSearch"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        >
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gradient-to-r from-indigo-500/10 to-purple-500/10 border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Assessment Name</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Type</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Class</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Max Score</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Date</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr 
                                    v-for="(assessment, index) in assessments?.data || []" 
                                    :key="assessment.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                                >
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        <div class="flex items-center justify-center w-8 h-8 bg-indigo-500/10 dark:bg-indigo-500/30 rounded-lg text-indigo-500 dark:text-indigo-400 font-semibold">
                                            {{ index + 1 }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ assessment.assessment_name || 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 capitalize">
                                            {{ assessment.assessment_type || 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ assessment.class_model?.name || 'N/A' }}</span>
                                            <span v-if="assessment.class_model?.code" class="text-gray-600 dark:text-gray-400 text-xs block">Code: {{ assessment.class_model.code }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-semibold">{{ assessment.max_score || 0 }}</span>
                                        <span v-if="assessment.score" class="text-gray-500 dark:text-gray-400 ml-2">({{ assessment.score }})</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ assessment.assessment_date ? new Date(assessment.assessment_date).toLocaleDateString() : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="[
                                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                            assessment.status === 'active'
                                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                                : 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                                        ]">
                                            {{ assessment.status || 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <Link
                                                :href="route('teacher.assessments.show', assessment.id)"
                                                class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                                title="View Details & Grade"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </Link>
                                            <Link
                                                :href="route('teacher.assessments.edit', assessment.id)"
                                                class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                                title="Edit"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <button
                                                @click="deleteAssessment(assessment.id)"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                                title="Delete"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!assessments?.data || assessments.data.length === 0">
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-clipboard-list text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                            <p>No assessments found.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <Pagination v-if="assessments?.links" :links="assessments.links"/>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>

