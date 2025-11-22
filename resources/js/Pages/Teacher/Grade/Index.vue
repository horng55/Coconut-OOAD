<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Grades", href: route("teacher.grades.index")}];

const props = defineProps({
    grades: Object,
    classes: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedClass = ref(props.filters?.class_id || "");

const handleSearch = () => {
    router.get(route('teacher.grades.index'), {
        search: searchQuery.value,
        class_id: selectedClass.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<template>
    <TeacherLayout :title="title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 p-6">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                                <i class="fas fa-graduation-cap"></i>
                                Grades Management
                            </h1>
                            <p class="text-blue-100 mt-1">Total: {{ grades?.total || 0 }} grade records</p>
                        </div>
                        <Link
                            :href="route('teacher.grades.create')"
                            class="bg-white text-blue-600 hover:bg-blue-50 px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 font-medium shadow-lg hover:shadow-xl"
                        >
                            <i class="fas fa-plus"></i>
                            Add Grade
                        </Link>
                    </div>
                </div>

                <div class="p-6">
                    <div v-if="classes && classes.length > 0" class="mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-blue-500 text-lg mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-sm text-blue-800 dark:text-blue-300 font-medium mb-1">
                                    Need to add students to your classes?
                                </p>
                                <p class="text-xs text-blue-700 dark:text-blue-400">
                                    Go to <Link :href="route('teacher.classes.index')" class="font-semibold underline hover:text-blue-600">My Classes</Link>, select a class, then use the "Create New" or "Enroll Existing" buttons to add students.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 flex-wrap mb-6">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Search student..."
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        />
                        <select
                            v-model="selectedClass"
                            @change="handleSearch"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        >
                            <option value="">All Classes</option>
                            <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                {{ classItem.name }} ({{ classItem.code }})
                            </option>
                        </select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gradient-to-r from-blue-500/10 to-cyan-500/10 border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Student</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Class</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Assessment</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Score</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Grade</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Date</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr 
                                    v-for="(grade, index) in grades?.data || []" 
                                    :key="grade.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                                >
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        <div class="flex items-center justify-center w-8 h-8 bg-blue-500/10 dark:bg-blue-500/30 rounded-lg text-blue-500 dark:text-blue-400 font-semibold">
                                            {{ index + 1 }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ grade.student?.user?.full_name || 'N/A' }}</span>
                                            <span v-if="grade.student?.student_id" class="text-gray-600 dark:text-gray-400 text-xs block">ID: {{ grade.student.student_id }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ grade.class_model?.name || 'N/A' }}</span>
                                            <span v-if="grade.class_model?.code" class="text-gray-600 dark:text-gray-400 text-xs block">Code: {{ grade.class_model.code }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ grade.assessment_name || 'N/A' }}</span>
                                            <span class="text-gray-600 dark:text-gray-400 text-xs block capitalize">{{ grade.assessment_type || 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-semibold">{{ grade.score || 0 }}/{{ grade.max_score || 0 }}</span>
                                        <span class="text-gray-500 dark:text-gray-400 ml-2">({{ grade.percentage || 0 }}%)</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="[
                                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold',
                                            grade.letter_grade === 'A' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                                            grade.letter_grade === 'B' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                            grade.letter_grade === 'C' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' :
                                            grade.letter_grade === 'D' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' :
                                            'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                        ]">
                                            {{ grade.letter_grade || 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ grade.assessment_date ? new Date(grade.assessment_date).toLocaleDateString() : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <Link
                                                :href="route('teacher.grades.edit', grade.id)"
                                                class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                                title="Edit"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!grades?.data || grades.data.length === 0">
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-graduation-cap text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                            <p>No grades found.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <Pagination v-if="grades?.links" :links="grades.links"/>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>

