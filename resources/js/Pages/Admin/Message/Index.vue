<script setup>
import {ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";
import Pagination from "../../../Components/Pagination.vue";

const title = [{label: "Messages", href: route("admin.messages.index")}];

const props = defineProps({
    messages: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const messageType = ref(props.filters?.type || "");
const isRead = ref(props.filters?.is_read || "");

const handleSearch = () => {
    router.get(route('admin.messages.index'), {
        search: searchQuery.value,
        type: messageType.value,
        is_read: isRead.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const deleteMessage = (id) => {
    if (confirm('Are you sure you want to delete this message?')) {
        router.delete(route('admin.messages.destroy', id));
    }
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Messages Management"
            :subtitle="`Total: ${messages?.total || 0} messages`"
            icon="fas fa-envelope"
            icon-color="text-violet-500"
            icon-bg="from-violet-500/20 to-purple-500/20"
        >
            <template #actions>
                <div class="flex items-center gap-3 flex-wrap">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search messages..."
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
                    />
                    <select
                        v-model="messageType"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
                    >
                        <option value="">All Messages</option>
                        <option value="sent">Sent</option>
                        <option value="received">Received</option>
                    </select>
                    <select
                        v-model="isRead"
                        @change="handleSearch"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
                    >
                        <option value="">All Status</option>
                        <option value="1">Read</option>
                        <option value="0">Unread</option>
                    </select>
                    <Link
                        :href="route('admin.messages.create')"
                        class="bg-gradient-to-r from-violet-500 to-purple-500 hover:from-violet-600 hover:to-purple-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-plus"></i>
                        New Message
                    </Link>
                </div>
            </template>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-violet-500/10 to-purple-500/10 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Subject</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">From</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">To</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Priority</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Date</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr 
                            v-for="(message, index) in messages?.data || []" 
                            :key="message.id"
                            :class="[
                                'hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200',
                                !message.is_read ? 'bg-blue-50/50 dark:bg-blue-900/10' : ''
                            ]"
                        >
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center justify-center w-8 h-8 bg-violet-500/10 dark:bg-violet-500/30 rounded-lg text-violet-500 dark:text-violet-400 font-semibold">
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <span :class="['font-medium', !message.is_read ? 'text-gray-900 dark:text-white font-bold' : 'text-gray-800 dark:text-gray-200']">
                                        {{ message.subject || 'N/A' }}
                                    </span>
                                    <span v-if="message.message" class="text-gray-600 dark:text-gray-400 text-xs block truncate max-w-xs">
                                        {{ message.message.substring(0, 50) }}...
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ message.sender?.full_name || message.sender?.email || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ message.receiver?.full_name || message.receiver?.email || 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                    message.priority === 'urgent' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' :
                                    message.priority === 'high' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' :
                                    message.priority === 'normal' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                    'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                                ]">
                                    <i :class="
                                        message.priority === 'urgent' ? 'fas fa-exclamation-triangle' :
                                        message.priority === 'high' ? 'fas fa-exclamation-circle' :
                                        'fas fa-info-circle'
                                    "></i>
                                    {{ message.priority || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                    message.is_read 
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                        : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300'
                                ]">
                                    <i :class="message.is_read ? 'fas fa-envelope-open' : 'fas fa-envelope'"></i>
                                    {{ message.is_read ? 'Read' : 'Unread' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ message.created_at ? new Date(message.created_at).toLocaleDateString() : 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <Link
                                        :href="route('admin.messages.show', message.id)"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="View"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <button
                                        @click="deleteMessage(message.id)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition-colors duration-200 flex items-center gap-1 text-sm"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!messages?.data || messages.data.length === 0">
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-envelope text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                                    <p>No messages found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="messages?.links" :links="messages.links"/>
        </AdminPageWrapper>
    </App>
</template>

