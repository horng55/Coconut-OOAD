<script setup>
import {ref, computed} from "vue";
import {router, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Timetables", href: route("admin.timetables.index")}];

const props = defineProps({
    timetables: Object,
    classes: Array,
    filters: Object,
    defaultAcademicYear: String,
});

const searchQuery = ref(props.filters?.search || "");
const selectedClassId = ref(props.filters?.class_id || "");
const selectedDay = ref(props.filters?.day_of_week || "");
const selectedAcademicYear = ref(props.filters?.academic_year || props.defaultAcademicYear);
const selectedStatus = ref(props.filters?.status || "");

const daysOfWeek = [
    { value: 'monday', label: 'Monday' },
    { value: 'tuesday', label: 'Tuesday' },
    { value: 'wednesday', label: 'Wednesday' },
    { value: 'thursday', label: 'Thursday' },
    { value: 'friday', label: 'Friday' },
    { value: 'saturday', label: 'Saturday' },
    { value: 'sunday', label: 'Sunday' },
];

const handleSearch = () => {
    router.get(route('admin.timetables.index'), {
        search: searchQuery.value,
        class_id: selectedClassId.value,
        day_of_week: selectedDay.value,
        academic_year: selectedAcademicYear.value,
        status: selectedStatus.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const deleteTimetable = (id) => {
    if (confirm('Are you sure you want to delete this timetable entry?')) {
        router.delete(route('admin.timetables.destroy', id));
    }
};

const formatTime = (time) => {
    if (!time) return 'N/A';
    const date = new Date('2000-01-01 ' + time);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
};

const getDayName = (day) => {
    return daysOfWeek.find(d => d.value === day)?.label || day;
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Timetable Management"
            :subtitle="`Total: ${timetables?.total || 0} timetable entries`"
            icon="fas fa-calendar-alt"
            icon-color="text-blue-500"
            icon-bg="from-blue-500/20 to-cyan-500/20"
        >
            <template #actions>
                <div class="flex items-center gap-3 flex-wrap">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search by class, subject, teacher, or room..."
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                    />
                    <select
                        v-model="selectedClassId"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                    >
                        <option value="">All Classes</option>
                        <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                            {{ classItem.name }} ({{ classItem.code }})
                        </option>
                    </select>
                    <select
                        v-model="selectedDay"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                    >
                        <option value="">All Days</option>
                        <option v-for="day in daysOfWeek" :key="day.value" :value="day.value">
                            {{ day.label }}
                        </option>
                    </select>
                    <input
                        v-model="selectedAcademicYear"
                        @change="handleSearch"
                        type="text"
                        placeholder="Academic Year"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50 w-40"
                    />
                    <select
                        v-model="selectedStatus"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                    >
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <Link
                        :href="route('admin.timetables.create')"
                        class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-plus"></i>
                        Add Timetable Entry
                    </Link>
                </div>
            </template>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-500/10 to-cyan-500/10 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Class</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Subject</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Teacher</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Day</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Time</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Room</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Academic Year</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr 
                            v-for="(timetable, index) in timetables?.data || []" 
                            :key="timetable.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center justify-center w-8 h-8 bg-blue-500/10 dark:bg-blue-500/30 rounded-lg text-blue-500 dark:text-blue-400 font-semibold">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ timetable.class_model?.name || 'N/A' }}</span>
                                    <span v-if="timetable.class_model?.code" class="text-gray-600 dark:text-gray-400 text-xs block">
                                        {{ timetable.class_model.code }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ timetable.subject?.name || 'N/A' }}</span>
                                    <span v-if="timetable.subject?.code" class="text-gray-600 dark:text-gray-400 text-xs block">
                                        {{ timetable.subject.code }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-800 dark:text-gray-200 font-medium">
                                    {{ timetable.teacher?.user?.full_name || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-800 dark:text-gray-200 font-medium">
                                    {{ getDayName(timetable.day_of_week) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-800 dark:text-gray-200 font-medium">
                                    {{ formatTime(timetable.start_time) }} - {{ formatTime(timetable.end_time) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ timetable.room || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ timetable.academic_year }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium',
                                    timetable.status === 'active' 
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                        : 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                                ]">
                                    <i :class="timetable.status === 'active' ? 'fas fa-check-circle' : 'fas fa-ban'"></i>
                                    {{ timetable.status ? timetable.status.charAt(0).toUpperCase() + timetable.status.slice(1) : 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="route('admin.timetables.show', timetable.id)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="View"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <Link
                                        :href="route('admin.timetables.edit', timetable.id)"
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button
                                        @click="deleteTimetable(timetable.id)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!timetables?.data || timetables.data.length === 0">
                            <td colspan="10" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-calendar-alt text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                    <p>No timetable entries found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="timetables?.links" :links="timetables.links"/>
        </AdminPageWrapper>
    </App>
</template>

