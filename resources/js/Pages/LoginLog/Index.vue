<script setup>
import {ref} from 'vue';
import {router} from '@inertiajs/vue3';
import App from "@/Layouts/App.vue";
import AdminPageWrapper from "@/Components/AdminPageWrapper.vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    paginate: Object,
    search: String,
    filters: Object,
});

const q = ref(props.search || '');
const selectedPortal = ref(props.filters?.portal || '');
const selectedActionType = ref(props.filters?.action_type || '');

const submit = () => {
    router.get(route('loginlogs.index'), {
        search: q.value,
        portal: selectedPortal.value,
        action_type: selectedActionType.value
    }, { preserveState: true, preserveScroll: true });
};
</script>

<template>
    <App :title="[{ label: 'Security', href: route('loginlogs.index') }]">
    <AdminPageWrapper
        title="Login Logs"
        :subtitle="`Total: ${paginate.total} logs`"
        icon="fas fa-user-shield"
        icon-color="text-indigo-500"
        icon-bg="from-indigo-500/20 to-purple-500/20"
    >
        <template #actions>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative">
                    <input
                        v-model="q"
                        @keyup.enter="submit"
                        type="search"
                        placeholder="Search username or IP"
                        class="w-64 px-4 py-2.5 pl-10 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all duration-300"
                    />
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                <select
                    v-model="selectedPortal"
                    @change="submit"
                    class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                >
                    <option value="">All Portals</option>
                    <option value="admin">Admin</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                    <option value="parent">Parent</option>
                </select>
                <select
                    v-model="selectedActionType"
                    @change="submit"
                    class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                >
                    <option value="">All Types</option>
                    <option value="login">Successful Login</option>
                    <option value="login_failed">Failed Login</option>
                </select>
                <button @click="submit" class="flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white px-5 py-2.5 rounded-xl font-medium shadow-lg hover:shadow-indigo-500/50 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-search"></i>
                    Search
                </button>
            </div>
        </template>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-indigo-500/10 to-purple-500/10 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <div class="flex items-center justify-center gap-2">
                                <i class="fas fa-hashtag text-indigo-500"></i>
                                #
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">User</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Portal</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Type</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">IP</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">User Agent</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Logged At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="(log, index) in paginate.data" :key="log.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400 font-semibold mx-auto">
                                {{ index + 1 }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div :class="[
                                    'w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0',
                                    log.action_type === 'login' 
                                        ? 'bg-gradient-to-br from-green-500/20 to-emerald-500/20' 
                                        : 'bg-gradient-to-br from-red-500/20 to-pink-500/20'
                                ]">
                                    <i :class="log.action_type === 'login' ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500'"></i>
                                </div>
                                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ log.user_username }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium capitalize',
                                log.portal === 'admin' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' :
                                log.portal === 'teacher' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                log.portal === 'student' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300' :
                                log.portal === 'parent' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300' :
                                'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300'
                            ]">
                                <i :class="
                                    log.portal === 'admin' ? 'fas fa-user-shield' :
                                    log.portal === 'teacher' ? 'fas fa-chalkboard-teacher' :
                                    log.portal === 'student' ? 'fas fa-user-graduate' :
                                    log.portal === 'parent' ? 'fas fa-users' :
                                    'fas fa-user'
                                "></i>
                                {{ log.portal || 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium',
                                log.action_type === 'login' 
                                    ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' 
                                    : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                            ]">
                                <i :class="log.action_type === 'login' ? 'fas fa-check' : 'fas fa-times'"></i>
                                {{ log.action_type === 'login' ? 'Success' : 'Failed' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-mono text-sm">{{ log.ip }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-sm max-w-[50ch] line-clamp-2 whitespace-normal">{{ (log.metadata && log.metadata.user_agent) ? log.metadata.user_agent : 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-sm">{{ new Date(log.created_at).toLocaleString() }}</td>
                    </tr>
                    <tr v-if="!paginate.data || paginate.data.length === 0">
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                    <i class="fas fa-inbox text-3xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">No logs found</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination v-if="paginate?.links" :links="paginate.links"/>
    </AdminPageWrapper>
    </App>
</template>


