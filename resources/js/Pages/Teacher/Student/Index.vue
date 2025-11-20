<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import TeacherLayout from "../../../Layouts/TeacherLayout.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "My Students", href: route("teacher.students.index")}];

const props = defineProps({
    students: Object,
    classes: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const selectedClass = ref(props.filters?.class_id || "");

const handleSearch = () => {
    router.get(route('teacher.students.index'), {
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
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-user-graduate text-white text-xl"></i>
                            </div>
                            My Students
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">View all students in your classes</p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Search students..."
                            class="flex-1 md:flex-none px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        />
                        <select
                            v-model="selectedClass"
                            @change="handleSearch"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                        >
                            <option value="">All Classes</option>
                            <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                {{ classItem.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div v-if="students?.data && students.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link
                        v-for="student in students.data"
                        :key="student.id"
                        :href="route('teacher.students.show', student.id)"
                        class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 border border-blue-200 dark:border-blue-800 hover:shadow-lg transition-all duration-200"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
                                    {{ student.user?.full_name || 'N/A' }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                                    {{ student.student_id || 'N/A' }}
                                </p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                        <div v-if="student.enrollments && student.enrollments.length > 0" class="space-y-2">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Classes:</p>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="enrollment in student.enrollments"
                                    :key="enrollment.id"
                                    class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs rounded"
                                >
                                    {{ enrollment.class_model?.name || 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <div v-else class="text-center py-12">
                    <i class="fas fa-user-graduate text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">No students found.</p>
                </div>

                <Pagination v-if="students?.links" :links="students.links" class="mt-6"/>
            </div>
        </div>
    </TeacherLayout>
</template>

