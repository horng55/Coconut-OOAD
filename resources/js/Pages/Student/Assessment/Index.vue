<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import StudentLayout from "../../../Layouts/StudentLayout.vue";

const route = window.route || ((name) => `#${name}`);

const title = [{label: "Assessments", href: "/student/assessments"}];

const props = defineProps({
    assessments: Object,
    classes: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedClass = ref(props.filters?.class_id || "");
const selectedType = ref(props.filters?.assessment_type || "");

const handleSearch = () => {
    router.get('/student/assessments', {
        search: searchQuery.value,
        class_id: selectedClass.value,
        assessment_type: selectedType.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const getTypeColor = (type) => {
    const colors = {
        quiz: 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
        assignment: 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300',
        exam: 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
        mid_term: 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300',
        project: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
        participation: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
    };
    return colors[type] || 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300';
};

const getGradeStatus = (assessment) => {
    const grade = assessment.grades?.[0];
    if (!grade) {
        return { text: 'Not Graded', color: 'text-gray-500 dark:text-gray-400', icon: 'fa-clock' };
    }
    const percentage = (grade.score / assessment.max_score) * 100;
    if (percentage >= 80) {
        return { text: 'Excellent', color: 'text-green-600 dark:text-green-400', icon: 'fa-check-circle' };
    } else if (percentage >= 60) {
        return { text: 'Good', color: 'text-blue-600 dark:text-blue-400', icon: 'fa-check-circle' };
    } else if (percentage >= 40) {
        return { text: 'Fair', color: 'text-orange-600 dark:text-orange-400', icon: 'fa-exclamation-circle' };
    } else {
        return { text: 'Needs Improvement', color: 'text-red-600 dark:text-red-400', icon: 'fa-times-circle' };
    }
};
</script>

<template>
    <StudentLayout :title="title">
        <div class="p-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-purple-500 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">My Assessments</h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">View all your assessments and grades</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-3">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search assessments..."
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                    />
                    <select
                        v-model="selectedClass"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                    >
                        <option value="">All Classes</option>
                        <option v-for="(classItem, idx) in classes" :key="classItem?.id || idx" :value="classItem?.id" v-if="classItem?.id">
                            {{ classItem?.name }}
                        </option>
                    </select>
                    <select
                        v-model="selectedType"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                    >
                        <option value="">All Types</option>
                        <option value="quiz">Quiz</option>
                        <option value="assignment">Assignment</option>
                        <option value="exam">Exam</option>
                        <option value="mid_term">Mid-Term</option>
                        <option value="project">Project</option>
                        <option value="participation">Participation</option>
                    </select>
                </div>
            </div>

            <!-- Assessments Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div
                    v-for="(assessment, index) in assessments?.data || []"
                    :key="assessment?.id || index"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 overflow-hidden"
                    v-if="assessment?.id"
                >
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">
                                    {{ assessment?.assessment_name || 'Untitled Assessment' }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ assessment?.class_model?.name || 'N/A' }}
                                </p>
                            </div>
                            <span :class="['px-3 py-1 rounded-full text-xs font-medium capitalize', getTypeColor(assessment?.assessment_type)]">
                                {{ assessment?.assessment_type?.replace('_', ' ') || 'N/A' }}
                            </span>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-calendar w-4"></i>
                                <span>{{ assessment?.assessment_date ? new Date(assessment.assessment_date).toLocaleDateString() : 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-star w-4"></i>
                                <span>Max Score: {{ assessment?.max_score || 0 }}</span>
                            </div>
                            
                            <!-- Submission Status -->
                            <div v-if="assessment?.submissions && assessment.submissions.length > 0" class="flex items-center gap-2 text-sm">
                                <i class="fas fa-check-circle w-4 text-green-600 dark:text-green-400"></i>
                                <span class="font-semibold text-green-600 dark:text-green-400">Submitted</span>
                            </div>
                            <div v-else class="flex items-center gap-2 text-sm">
                                <i class="fas fa-exclamation-circle w-4 text-orange-600 dark:text-orange-400"></i>
                                <span class="font-semibold text-orange-600 dark:text-orange-400">Not Submitted</span>
                            </div>
                            
                            <!-- Grade Status -->
                            <div v-if="assessment?.grades?.[0] && assessment.grades[0].score > 0" class="flex items-center gap-2 text-sm">
                                <i :class="['fas w-4', getGradeStatus(assessment).icon, getGradeStatus(assessment).color]"></i>
                                <span :class="['font-semibold', getGradeStatus(assessment).color]">
                                    Your Score: {{ assessment.grades[0].score }} / {{ assessment?.max_score || 0 }}
                                    ({{ ((assessment.grades[0].score / (assessment?.max_score || 1)) * 100).toFixed(1) }}%)
                                </span>
                            </div>
                            <div v-else-if="assessment?.submissions && assessment.submissions.length > 0" class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-clock w-4"></i>
                                <span>Waiting for grade</span>
                            </div>
                            <div v-else class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-clock w-4"></i>
                                <span>Not graded yet</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <Link
                                v-if="!assessment?.submissions || assessment.submissions.length === 0"
                                :href="`/student/assessments/${assessment?.id}`"
                                class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-center py-3 rounded-lg transition-all duration-200 font-semibold flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                            >
                                <i class="fas fa-upload"></i>
                                Upload PDF
                            </Link>
                            <Link
                                v-else
                                :href="`/student/assessments/${assessment?.id}`"
                                class="flex-1 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white text-center py-3 rounded-lg transition-all duration-200 font-semibold flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                            >
                                <i class="fas fa-check-circle"></i>
                                View Submission
                            </Link>
                            <Link
                                :href="`/student/assessments/${assessment?.id}`"
                                class="px-4 bg-gray-500 hover:bg-gray-600 text-white text-center py-3 rounded-lg transition-all duration-200 font-medium flex items-center justify-center"
                            >
                                <i class="fas fa-eye"></i>
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-if="!assessments?.data || assessments.data.length === 0" class="col-span-full">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                        <i class="fas fa-clipboard-list text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-lg">No assessments found</p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="assessments?.links" class="mt-6 flex justify-center gap-2">
                <Link
                    v-for="(link, index) in assessments.links"
                    :key="index"
                    :href="link?.url"
                    v-if="link?.url !== null"
                    :class="[
                        'px-4 py-2 rounded-lg transition-colors duration-200',
                        link.active
                            ? 'bg-purple-500 text-white'
                            : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </StudentLayout>
</template>
