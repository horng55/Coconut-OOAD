<script setup>
import {ref} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../Layouts/App.vue";
import AdminPageWrapper from "../../Components/AdminPageWrapper.vue";

const route = window.route || ((name, params) => {
    if (params) return `/${name.replace('.', '/')}/${params}`;
    return `/${name.replace('.', '/')}`;
});

const title = [
    {label: "Users", href: "/users"},
    {label: "Edit User", href: "#"}
];

const props = defineProps({
    user: Object,
    roles: Array,
});

const form = useForm({
    first_name: props.user?.first_name || '',
    last_name: props.user?.last_name || '',
    username: props.user?.username || '',
    email: props.user?.email || '',
    password: '',
    password_confirmation: '',
    phone_number: props.user?.phone_number || '',
    gender: props.user?.gender || '',
    dob: props.user?.dob || '',
    type: props.user?.type || 'user',
    profile: null,
    roles: props.user?.roles ? props.user.roles.map(r => r.id) : [],
});

const submit = () => {
    form.post(`/users/${props.user.id}`, {
        onSuccess: () => {
            router.visit('/users');
        }
    });
};

const handleFileChange = (event) => {
    form.profile = event.target.files[0];
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Edit User"
            :subtitle="`Editing: ${user?.full_name || user?.first_name + ' ' + user?.last_name || 'User'}`"
            icon="fas fa-edit"
            icon-color="text-amber-500"
            icon-bg="from-amber-500/20 to-orange-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.first_name"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">{{ form.errors.first_name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.last_name"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.last_name" class="text-red-500 text-sm mt-1">{{ form.errors.last_name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.username"
                            type="text"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.username" class="text-red-500 text-sm mt-1">{{ form.errors.username }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Password (leave blank to keep current)
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            minlength="6"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                    </div>

                    <div v-if="form.password">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Confirm Password
                        </label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            minlength="6"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.password_confirmation" class="text-red-500 text-sm mt-1">{{ form.errors.password_confirmation }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Phone Number
                        </label>
                        <input
                            v-model="form.phone_number"
                            type="tel"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.phone_number" class="text-red-500 text-sm mt-1">{{ form.errors.phone_number }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Gender
                        </label>
                        <select
                            v-model="form.gender"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        >
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <div v-if="form.errors.gender" class="text-red-500 text-sm mt-1">{{ form.errors.gender }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date of Birth
                        </label>
                        <input
                            v-model="form.dob"
                            type="date"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.dob" class="text-red-500 text-sm mt-1">{{ form.errors.dob }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            User Type
                        </label>
                        <select
                            v-model="form.type"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        >
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="editor">Editor</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                            <option value="parent">Parent</option>
                        </select>
                        <div v-if="form.errors.type" class="text-red-500 text-sm mt-1">{{ form.errors.type }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Roles
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-48 overflow-y-auto p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800">
                            <label
                                v-for="role in roles || []"
                                :key="role.id"
                                class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                            >
                                <input
                                    type="checkbox"
                                    :value="role.id"
                                    v-model="form.roles"
                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700"
                                />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ role.name }}</span>
                            </label>
                            <p v-if="!roles || roles.length === 0" class="col-span-full text-sm text-gray-500 dark:text-gray-400 text-center py-2">
                                No roles available.
                            </p>
                        </div>
                        <div v-if="form.errors.roles" class="text-red-500 text-sm mt-1">{{ form.errors.roles }}</div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Select one or more roles to assign to this user</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Profile Picture
                        </label>
                        <input
                            @change="handleFileChange"
                            type="file"
                            accept="image/jpeg,image/png,image/jpg,image/gif"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500/50"
                        />
                        <div v-if="form.errors.profile" class="text-red-500 text-sm mt-1">{{ form.errors.profile }}</div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('users.index')"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-save"></i>
                        {{ form.processing ? 'Updating...' : 'Update User' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

