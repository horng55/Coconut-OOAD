<script setup>
import {ref} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Announcements", href: route("admin.announcements.index")},
    {label: "Create Announcement", href: route("admin.announcements.create")}
];

const props = defineProps({
    classes: Array,
});

const form = useForm({
    title: '',
    content: '',
    target_audience: ['all'],
    class_id: '',
    publish_date: new Date().toISOString().split('T')[0],
    expiry_date: '',
    is_pinned: false,
    priority: 'normal',
});

const handleAllChange = () => {
    if (form.target_audience.includes('all')) {
        // If "All" is checked, uncheck others and only keep "all"
        form.target_audience = ['all'];
    }
};

const handlePortalChange = () => {
    // If a specific portal is checked, remove "all" if it exists
    if (form.target_audience.includes('all')) {
        form.target_audience = form.target_audience.filter(item => item !== 'all');
    }
    // Ensure at least one is selected
    if (form.target_audience.length === 0) {
        form.target_audience = ['all'];
    }
};

const submit = () => {
    form.post(route('admin.announcements.store'), {
        onSuccess: () => {
            router.visit(route('admin.announcements.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Create New Announcement"
            subtitle="Post a new announcement to the school"
            icon="fas fa-bullhorn"
            icon-color="text-rose-500"
            icon-bg="from-rose-500/20 to-pink-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            required
                            placeholder="Announcement title"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50"
                        />
                        <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Content <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="form.content"
                            rows="6"
                            required
                            placeholder="Announcement content..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50"
                        ></textarea>
                        <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Target Audience (Portals) <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <label class="flex items-center space-x-3 p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input
                                    type="checkbox"
                                    :value="'all'"
                                    v-model="form.target_audience"
                                    @change="handleAllChange"
                                    class="w-4 h-4 text-rose-500 bg-gray-100 border-gray-300 rounded focus:ring-rose-500 focus:ring-2"
                                />
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">All Portals</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input
                                    type="checkbox"
                                    :value="'teachers'"
                                    v-model="form.target_audience"
                                    @change="handlePortalChange"
                                    class="w-4 h-4 text-rose-500 bg-gray-100 border-gray-300 rounded focus:ring-rose-500 focus:ring-2"
                                />
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Teacher Portal</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input
                                    type="checkbox"
                                    :value="'students'"
                                    v-model="form.target_audience"
                                    @change="handlePortalChange"
                                    class="w-4 h-4 text-rose-500 bg-gray-100 border-gray-300 rounded focus:ring-rose-500 focus:ring-2"
                                />
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Student Portal</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input
                                    type="checkbox"
                                    :value="'parents'"
                                    v-model="form.target_audience"
                                    @change="handlePortalChange"
                                    class="w-4 h-4 text-rose-500 bg-gray-100 border-gray-300 rounded focus:ring-rose-500 focus:ring-2"
                                />
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Parent Portal</span>
                            </label>
                        </div>
                        <div v-if="form.errors.target_audience" class="text-red-500 text-sm mt-1">{{ form.errors.target_audience }}</div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <i class="fas fa-info-circle"></i> Select one or more portals to target this announcement
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Class (Optional)
                        </label>
                        <select
                            v-model="form.class_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50"
                        >
                            <option value="">All Classes</option>
                            <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                {{ classItem.name }} ({{ classItem.code }})
                            </option>
                        </select>
                        <div v-if="form.errors.class_id" class="text-red-500 text-sm mt-1">{{ form.errors.class_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Publish Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.publish_date"
                            type="date"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50"
                        />
                        <div v-if="form.errors.publish_date" class="text-red-500 text-sm mt-1">{{ form.errors.publish_date }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Expiry Date (Optional)
                        </label>
                        <input
                            v-model="form.expiry_date"
                            type="date"
                            :min="form.publish_date"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50"
                        />
                        <div v-if="form.errors.expiry_date" class="text-red-500 text-sm mt-1">{{ form.errors.expiry_date }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Priority <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.priority"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-rose-500/50"
                        >
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                        <div v-if="form.errors.priority" class="text-red-500 text-sm mt-1">{{ form.errors.priority }}</div>
                    </div>

                    <div class="flex items-center">
                        <input
                            v-model="form.is_pinned"
                            type="checkbox"
                            id="is_pinned"
                            class="w-4 h-4 text-rose-500 bg-gray-100 border-gray-300 rounded focus:ring-rose-500 focus:ring-2"
                        />
                        <label for="is_pinned" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Pin this announcement
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.announcements.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-save"></i>
                        {{ form.processing ? 'Creating...' : 'Create Announcement' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

