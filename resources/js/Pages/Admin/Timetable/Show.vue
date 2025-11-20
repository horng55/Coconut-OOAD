<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Timetables", href: route("admin.timetables.index")},
    {label: "Timetable Details", href: "#"}
];

const props = defineProps({
    timetable: Object,
});

const daysOfWeek = [
    { value: 'monday', label: 'Monday' },
    { value: 'tuesday', label: 'Tuesday' },
    { value: 'wednesday', label: 'Wednesday' },
    { value: 'thursday', label: 'Thursday' },
    { value: 'friday', label: 'Friday' },
    { value: 'saturday', label: 'Saturday' },
    { value: 'sunday', label: 'Sunday' },
];

const getDayName = (day) => {
    return daysOfWeek.find(d => d.value === day)?.label || day;
};

const formatTime = (time) => {
    if (!time) return 'N/A';
    const date = new Date('2000-01-01 ' + time);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            :title="`Timetable Entry Details`"
            subtitle="View timetable entry information"
            icon="fas fa-calendar-alt"
            icon-color="text-blue-500"
            icon-bg="from-blue-500/20 to-cyan-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.timetables.index')"
                        class="text-blue-500 hover:text-blue-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Timetables
                    </Link>
                    <Link
                        :href="route('admin.timetables.edit', timetable?.id)"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-edit"></i>
                        Edit Timetable Entry
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Class</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ timetable?.class_model?.name || 'N/A' }}
                        </p>
                        <p v-if="timetable?.class_model?.code" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Code: {{ timetable.class_model.code }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Subject</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ timetable?.subject?.name || 'N/A' }}
                        </p>
                        <p v-if="timetable?.subject?.code" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Code: {{ timetable.subject.code }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Teacher</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ timetable?.teacher?.user?.full_name || 'N/A' }}
                        </p>
                        <p v-if="timetable?.teacher?.employee_id" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Employee ID: {{ timetable.teacher.employee_id }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Day of Week</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ getDayName(timetable?.day_of_week) }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Time</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ formatTime(timetable?.start_time) }} - {{ formatTime(timetable?.end_time) }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Room</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ timetable?.room || 'Not specified' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Academic Year</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ timetable?.academic_year || 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h3>
                        <span :class="[
                            'inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium',
                            timetable?.status === 'active' 
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                : 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                        ]">
                            <i :class="timetable?.status === 'active' ? 'fas fa-check-circle' : 'fas fa-ban'"></i>
                            {{ timetable?.status ? timetable.status.charAt(0).toUpperCase() + timetable.status.slice(1) : 'N/A' }}
                        </span>
                    </div>

                    <div v-if="timetable?.notes" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</h3>
                        <p class="text-gray-900 dark:text-white">{{ timetable.notes }}</p>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

