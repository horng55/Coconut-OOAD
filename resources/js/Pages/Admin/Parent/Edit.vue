<script setup>
import {ref} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Parents", href: route("admin.parents.index")},
    {label: "Edit Parent", href: "#"}
];

const props = defineProps({
    parent: Object,
});

const emergencyContactValue = ref(
    props.parent?.emergency_contact 
        ? (typeof props.parent.emergency_contact === 'object' 
            ? props.parent.emergency_contact.phone 
            : props.parent.emergency_contact)
        : ''
);

const form = useForm({
    first_name: props.parent?.user?.first_name || '',
    last_name: props.parent?.user?.last_name || '',
    username: props.parent?.user?.username || '',
    email: props.parent?.user?.email || '',
    password: '',
    relationship: props.parent?.relationship || '',
    address: props.parent?.address || '',
    emergency_contact: emergencyContactValue.value,
    phone_number: props.parent?.user?.phone_number || '',
    status: props.parent?.user?.status || 'active',
});

const submit = () => {
    form.put(route('admin.parents.update', props.parent.id), {
        onSuccess: () => {
            router.visit(route('admin.parents.index'));
        }
    });
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Edit Parent"
            :subtitle="`Editing: ${parent?.user?.full_name || parent?.user?.first_name + ' ' + parent?.user?.last_name || 'Parent'}`"
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
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
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
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
                            minlength="8"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        />
                        <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Relationship <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.relationship"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        >
                            <option value="">Select Relationship</option>
                            <option value="father">Father</option>
                            <option value="mother">Mother</option>
                            <option value="guardian">Guardian</option>
                        </select>
                        <div v-if="form.errors.relationship" class="text-red-500 text-sm mt-1">{{ form.errors.relationship }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Phone Number
                        </label>
                        <input
                            v-model="form.phone_number"
                            type="tel"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        />
                        <div v-if="form.errors.phone_number" class="text-red-500 text-sm mt-1">{{ form.errors.phone_number }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.status"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        >
                            <option value="active">Active</option>
                            <option value="disable">Disable</option>
                        </select>
                        <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Address
                        </label>
                        <textarea
                            v-model="form.address"
                            rows="3"
                            placeholder="Full address"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        ></textarea>
                        <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Emergency Contact
                        </label>
                        <input
                            v-model="form.emergency_contact"
                            type="tel"
                            placeholder="Emergency contact phone number"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                        />
                        <div v-if="form.errors.emergency_contact" class="text-red-500 text-sm mt-1">{{ form.errors.emergency_contact }}</div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.parents.index')"
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
                        {{ form.processing ? 'Updating...' : 'Update Parent' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>

