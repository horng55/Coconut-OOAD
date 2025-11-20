<script setup>
import {Link} from "@inertiajs/vue3";
import {computed} from "vue";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import EventHelper from "../../../Helper/EventHelper.js";
import DateHelper from "../../../Helper/DateHelper.js";

const props = defineProps({
    event: Object,
});

const title = [
    {label: "Events", href: route("admin.events.index")},
    {label: "Event Details", href: route("admin.events.show", props.event?.id)}
];

const parseBackground = (background) => {
    if (!background) return null;
    if (typeof background === 'object') return background;
    try {
        return JSON.parse(background);
    } catch {
        return null;
    }
};

const getEventTypeLabel = (type) => {
    const types = {
        'wedding': 'Wedding',
        'birthday': 'Birthday',
        'housing': 'Housing',
        'party': 'Party',
    };
    return types[type] || type;
};

const eventBackground = parseBackground(props.event?.background);
const backgroundUrl = eventBackground?.path || null;

const eventPreviewUrl = computed(() => {
    if (!props.event?.slug) return '';
    return `${window.location.origin}/event/preview/${props.event.slug}`;
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Event Details"
            :subtitle="`Viewing event information`"
            icon="fas fa-calendar-alt"
            icon-color="text-blue-500"
            icon-bg="from-blue-500/20 to-cyan-500/20"
        >
            <template #actions>
                <Link
                    :href="route('admin.events.index')"
                    class="flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-5 py-2.5 rounded-xl font-medium shadow-md transition-all duration-300 transform hover:scale-105"
                >
                    <i class="fas fa-arrow-left"></i>
                    Back to List
                </Link>
            </template>

            <div class="space-y-6">
                <!-- Event Background Image -->
                <div v-if="backgroundUrl" class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                    <img 
                        :src="backgroundUrl" 
                        :alt="event.name || 'Event background'"
                        class="w-full h-96 object-cover"
                        @error="$event.target.src = '/cth_logo.png'"
                    />
                </div>

                <!-- Event Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 space-y-6">
                    <!-- Basic Information -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            Event Information
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Event Name</label>
                                <p class="text-gray-800 dark:text-white font-medium text-lg">{{ event.name || 'N/A' }}</p>
                            </div>
                            <div v-if="event.additional_name">
                                <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Additional Name</label>
                                <p class="text-gray-800 dark:text-white">{{ event.additional_name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Event Type</label>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 rounded-full text-sm font-medium">
                                        <i class="fas fa-tag"></i>
                                        {{ getEventTypeLabel(event.type) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Event Date</label>
                                <p class="text-gray-800 dark:text-white font-medium">{{ DateHelper.dateFormat(event.date) }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Phone</label>
                                <p class="text-gray-800 dark:text-white">
                                    <a :href="`tel:${event.phone}`" class="text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ event.phone || 'N/A' }}
                                    </a>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Location</label>
                                <p class="text-gray-800 dark:text-white">{{ event.location || 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="event.description" class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-align-left text-green-500"></i>
                            Description
                        </h2>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-gray-800 dark:text-white whitespace-pre-wrap">{{ event.description }}</p>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6" v-if="event.user">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-user text-indigo-500"></i>
                            User Information
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Username</label>
                                <p class="text-gray-800 dark:text-white font-medium">{{ event.user_username || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">User Name</label>
                                <p class="text-gray-800 dark:text-white">{{ event.user?.name || 'N/A' }}</p>
                            </div>
                            <div v-if="event.user?.email">
                                <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Email</label>
                                <p class="text-gray-800 dark:text-white">{{ event.user.email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Event Slug/Link -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6" v-if="event.slug">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-link text-orange-500"></i>
                            Event Link
                        </h2>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-gray-800 dark:text-white font-mono text-sm break-all">
                                {{ eventPreviewUrl }}
                            </p>
                            <a 
                                :href="eventPreviewUrl"
                                target="_blank"
                                class="inline-flex items-center gap-2 mt-2 text-blue-600 dark:text-blue-400 hover:underline"
                            >
                                <i class="fas fa-external-link-alt"></i>
                                View Public Event Page
                            </a>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="pt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
                            <div>
                                <label class="font-semibold">Created At</label>
                                <p>{{ event.created_at ? new Date(event.created_at).toLocaleString() : 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="font-semibold">Updated At</label>
                                <p>{{ event.updated_at ? new Date(event.updated_at).toLocaleString() : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

<style scoped>
</style>

