<script setup>
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import StudentLayout from "../../../Layouts/StudentLayout.vue";

const title = [{label: "My Timetable", href: route("student.timetable.index")}];

const props = defineProps({
    timetables: Array,
    groupedTimetables: Object,
    classes: Array,
    daysOfWeek: Array,
    filters: Object,
});

const selectedClass = ref(props.filters?.class_id || "");
const selectedDay = ref(props.filters?.day_of_week || "");
const academicYear = ref(props.filters?.academic_year || "");

const handleSearch = () => {
    router.get(route('student.timetable.index'), {
        class_id: selectedClass.value,
        day_of_week: selectedDay.value,
        academic_year: academicYear.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<template>
    <StudentLayout :title="title">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-white text-xl"></i>
                            </div>
                            My Timetable
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">View your class schedule</p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto flex-wrap">
                        <select
                            v-model="selectedClass"
                            @change="handleSearch"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        >
                            <option value="">All Classes</option>
                            <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                {{ classItem.name }}
                            </option>
                        </select>
                        <select
                            v-model="selectedDay"
                            @change="handleSearch"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        >
                            <option value="">All Days</option>
                            <option v-for="day in daysOfWeek" :key="day" :value="day">
                                {{ day }}
                            </option>
                        </select>
                        <input
                            v-model="academicYear"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Academic Year"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                    </div>
                </div>

                <div v-if="groupedTimetables && Object.keys(groupedTimetables).length > 0" class="space-y-6">
                    <div
                        v-for="day in daysOfWeek"
                        :key="day"
                        v-show="groupedTimetables[day] && groupedTimetables[day].length > 0"
                        class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6"
                    >
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-calendar-day text-emerald-500"></i>
                            {{ day }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div
                                v-for="timetable in groupedTimetables[day]"
                                :key="timetable.id"
                                class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200"
                            >
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900 dark:text-white">
                                            {{ timetable.subject?.name || 'N/A' }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ timetable.class_model?.name || 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-clock text-emerald-500"></i>
                                        {{ timetable.start_time }} - {{ timetable.end_time }}
                                    </div>
                                    <div v-if="timetable.teacher?.user" class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-user text-blue-500"></i>
                                        {{ timetable.teacher.user.full_name }}
                                    </div>
                                    <div v-if="timetable.room" class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-door-open text-purple-500"></i>
                                        Room: {{ timetable.room }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <i class="fas fa-calendar-alt text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">No timetable found.</p>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

