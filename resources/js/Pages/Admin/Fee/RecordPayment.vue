<script setup>
import {computed} from "vue";
import {router, useForm, Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Fees", href: route("admin.fees.index")},
    {label: "Record Payment", href: "#"}
];

const props = defineProps({
    fee: Object,
    feePayment: Object,
});

const form = useForm({
    amount_paid: '',
    payment_date: new Date().toISOString().split('T')[0],
    payment_method: 'cash',
    transaction_id: '',
    receipt_number: '',
    notes: '',
});

const submit = () => {
    form.put(route('admin.fees.payments.update', [props.fee.id, props.feePayment.id]), {
        onSuccess: () => {
            router.visit(route('admin.fees.show', props.fee.id));
        }
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    }).format(amount || 0);
};

const remainingAmount = computed(() => {
    const currentPaid = parseFloat(props.feePayment?.amount_paid || 0);
    const newPaid = parseFloat(form.amount_paid || 0);
    const total = parseFloat(props.feePayment?.amount || 0);
    return Math.max(0, total - (currentPaid + newPaid));
});
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Record Payment"
            :subtitle="`For: ${fee?.name || 'Fee'}`"
            icon="fas fa-money-bill-wave"
            icon-color="text-green-500"
            icon-bg="from-green-500/20 to-emerald-500/20"
        >
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <!-- Payment Summary -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Payment Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Amount</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(feePayment?.amount) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Already Paid</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(feePayment?.amount_paid) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Remaining</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(feePayment?.remaining_amount) }}</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Student</p>
                        <p class="font-semibold text-gray-900 dark:text-white">
                            {{ feePayment?.student?.user?.full_name || 'N/A' }} ({{ feePayment?.student?.student_id || 'N/A' }})
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Amount to Pay <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.amount_paid"
                            type="number"
                            step="0.01"
                            min="0"
                            :max="feePayment?.remaining_amount"
                            required
                            placeholder="0.00"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Maximum: {{ formatCurrency(feePayment?.remaining_amount) }}
                        </p>
                        <div v-if="form.errors.amount_paid" class="text-red-500 text-sm mt-1">{{ form.errors.amount_paid }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Payment Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.payment_date"
                            type="date"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        />
                        <div v-if="form.errors.payment_date" class="text-red-500 text-sm mt-1">{{ form.errors.payment_date }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.payment_method"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        >
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="online">Online</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                        <div v-if="form.errors.payment_method" class="text-red-500 text-sm mt-1">{{ form.errors.payment_method }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Transaction ID
                        </label>
                        <input
                            v-model="form.transaction_id"
                            type="text"
                            placeholder="Optional transaction reference"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        />
                        <div v-if="form.errors.transaction_id" class="text-red-500 text-sm mt-1">{{ form.errors.transaction_id }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Receipt Number
                        </label>
                        <input
                            v-model="form.receipt_number"
                            type="text"
                            placeholder="Auto-generated if empty"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        />
                        <div v-if="form.errors.receipt_number" class="text-red-500 text-sm mt-1">{{ form.errors.receipt_number }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            placeholder="Optional payment notes"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                        ></textarea>
                        <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">{{ form.errors.notes }}</div>
                    </div>
                </div>

                <div v-if="form.amount_paid" class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">After this payment:</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Paid:</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ formatCurrency((parseFloat(feePayment?.amount_paid || 0) + parseFloat(form.amount_paid || 0))) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Remaining:</p>
                            <p class="text-lg font-bold" :class="remainingAmount <= 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white'">
                                {{ formatCurrency(remainingAmount) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        :href="route('admin.fees.show', fee?.id)"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing || !form.amount_paid || parseFloat(form.amount_paid) <= 0"
                        class="px-6 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-money-bill-wave"></i>
                        {{ form.processing ? 'Recording...' : 'Record Payment' }}
                    </button>
                </div>
            </form>
        </AdminPageWrapper>
    </App>
</template>


