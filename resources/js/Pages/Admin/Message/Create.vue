<script setup>
import {ref} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Messages", href: route("admin.messages.index")},
    {label: "Create Message", href: route("admin.messages.create")}
];

const props = defineProps({
    users: Array,
    receiver: Object,
});

const form = useForm({
    receiver_id: props.receiver?.id || '',
    subject: '',
    message: '',
    priority: 'normal',
});

const submit = () => {
    form.post(route('admin.messages.store'), {
        onSuccess: () => {
            router.visit(route('admin.messages.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Create New Message"
            subtitle="Send a message to another user"
            icon="fas fa-envelope"
            icon-color="text-violet-500"
            icon-bg="from-violet-500/20 to-purple-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            To (Recipient) <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.receiver_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
                        >
                            <option value="">Select Recipient</option>
                            <optgroup v-if="users.filter(u => u.type === 'teacher').length > 0" label="👨‍🏫 Teacher Portal">
                                <option v-for="user in users.filter(u => u.type === 'teacher')" :key="user.id" :value="user.id">
                                    {{ user.full_name || user.email }} ({{ user.email }})
                                </option>
                            </optgroup>
                            <optgroup v-if="users.filter(u => u.type === 'student').length > 0" label="👨‍🎓 Student Portal">
                                <option v-for="user in users.filter(u => u.type === 'student')" :key="user.id" :value="user.id">
                                    {{ user.full_name || user.email }} ({{ user.email }})
                                </option>
                            </optgroup>
                            <optgroup v-if="users.filter(u => u.type === 'parent').length > 0" label="👨‍👩‍👧 Parent Portal">
                                <option v-for="user in users.filter(u => u.type === 'parent')" :key="user.id" :value="user.id">
                                    {{ user.full_name || user.email }} ({{ user.email }})
                                </option>
                            </optgroup>
                            <optgroup v-if="users.filter(u => u.type === 'admin').length > 0" label="👤 Admin Portal">
                                <option v-for="user in users.filter(u => u.type === 'admin')" :key="user.id" :value="user.id">
                                    {{ user.full_name || user.email }} ({{ user.email }})
                                </option>
                            </optgroup>
                        </select>
                        <div v-if="form.errors.receiver_id" class="text-red-500 text-sm mt-1">{{ form.errors.receiver_id }}</div>
                        <p v-if="receiver" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Selected: <span class="font-medium">{{ receiver.full_name || receiver.email }}</span>
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.subject"
                            type="text"
                            required
                            placeholder="Message subject"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
                        />
                        <div v-if="form.errors.subject" class="text-red-500 text-sm mt-1">{{ form.errors.subject }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Priority <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.priority"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
                        >
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                        <div v-if="form.errors.priority" class="text-red-500 text-sm mt-1">{{ form.errors.priority }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="form.message"
                            rows="8"
                            required
                            placeholder="Type your message here..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-500/50"
                        ></textarea>
                        <div v-if="form.errors.message" class="text-red-500 text-sm mt-1">{{ form.errors.message }}</div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.messages.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-violet-500 to-purple-500 hover:from-violet-600 hover:to-purple-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-paper-plane"></i>
                        {{ form.processing ? 'Sending...' : 'Send Message' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

