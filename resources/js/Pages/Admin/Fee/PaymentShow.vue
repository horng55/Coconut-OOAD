<script setup>
import {Link} from "@inertiajs/vue3";
import App from "../../../Layouts/App.vue";
import AdminPageWrapper from "../../../Components/AdminPageWrapper.vue";

const title = [
    {label: "Fee Payments", href: route("admin.fee-payments.index")},
    {label: "Payment Details", href: "#"}
];

const props = defineProps({
    payment: Object,
    paymentHistory: Array,
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    }).format(amount || 0);
};

const getPaymentMethodIcon = (method) => {
    const icons = {
        'bank_transfer': 'fas fa-university',
        'cash': 'fas fa-money-bill',
        'card': 'fas fa-credit-card',
        'online': 'fas fa-globe',
        'cheque': 'fas fa-file-invoice',
        'other': 'fas fa-money-check'
    };
    return icons[method] || 'fas fa-money-check';
};

const getPaymentMethodLabel = (method) => {
    if (!method) return 'N/A';
    return method.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
};
</script>

<template>
    <App :title="title">
        <AdminPageWrapper
            title="Payment Details"
            subtitle="View complete payment information"
            icon="fas fa-receipt"
            icon-color="text-green-500"
            icon-bg="from-green-500/20 to-emerald-500/20"
        >
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <Link
                        :href="route('admin.fee-payments.index', { fee_id: payment?.fee_id, student_id: payment?.student_id })"
                        class="text-green-500 hover:text-green-600 flex items-center gap-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back to Payments
                    </Link>
                    <div class="flex items-center gap-3">
                        <Link
                            v-if="payment?.status !== 'paid' && payment?.fee"
                            :href="route('admin.fees.payments.record', [payment.fee_id, payment.id])"
                            class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-money-bill"></i>
                            Record Payment
                        </Link>
                        <Link
                            v-if="payment?.fee"
                            :href="route('admin.fees.show', payment.fee_id)"
                            class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white px-5 py-2 rounded-lg transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-eye"></i>
                            View Fee
                        </Link>
                    </div>
                </div>

                <!-- Payment Summary Card -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Amount</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(payment?.amount) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Amount Paid</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(payment?.amount_paid) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Remaining Amount</p>
                            <p class="text-3xl font-bold" :class="payment?.remaining_amount > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
                                {{ formatCurrency(payment?.remaining_amount) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-user text-blue-500"></i>
                            Student Information
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Student Name</p>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ payment?.student?.user?.full_name || 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Student ID</p>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ payment?.student?.student_id || 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Email</p>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ payment?.student?.user?.email || 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-yellow-500"></i>
                            Fee Information
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Fee Name</p>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ payment?.fee?.name || 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Fee Type</p>
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full text-sm font-medium capitalize">
                                    {{ payment?.fee?.fee_type || 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Class</p>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ payment?.fee?.class_model?.name || 'All Classes' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Academic Year</p>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ payment?.academic_year || 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-credit-card text-green-500"></i>
                            Payment Details
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Payment Status</p>
                                <span :class="[
                                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium',
                                    payment?.status === 'paid' 
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                        : payment?.status === 'partial'
                                        ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300'
                                        : payment?.status === 'overdue'
                                        ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                        : payment?.status === 'waived'
                                        ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
                                ]">
                                    <i :class="[
                                        payment?.status === 'paid' ? 'fas fa-check-circle' :
                                        payment?.status === 'partial' ? 'fas fa-exclamation-circle' :
                                        payment?.status === 'overdue' ? 'fas fa-times-circle' :
                                        payment?.status === 'waived' ? 'fas fa-hand-holding-heart' :
                                        'fas fa-clock'
                                    ]"></i>
                                    {{ payment?.status ? payment.status.charAt(0).toUpperCase() + payment.status.slice(1) : 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Payment Method</p>
                                <span v-if="payment?.payment_method" class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full text-sm font-medium">
                                    <i :class="getPaymentMethodIcon(payment.payment_method)"></i>
                                    {{ getPaymentMethodLabel(payment.payment_method) }}
                                </span>
                                <span v-else class="text-gray-400 dark:text-gray-500">N/A</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Transaction ID</p>
                                <p v-if="payment?.transaction_id" class="text-base font-mono text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded inline-block">
                                    {{ payment.transaction_id }}
                                </p>
                                <p v-else class="text-gray-400 dark:text-gray-500">N/A</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Receipt Number</p>
                                <p v-if="payment?.receipt_number" class="text-base font-mono text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded inline-block">
                                    {{ payment.receipt_number }}
                                </p>
                                <p v-else class="text-gray-400 dark:text-gray-500">N/A</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Due Date</p>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ payment?.due_date ? new Date(payment.due_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Payment Date</p>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ payment?.payment_date ? new Date(payment.payment_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Not paid yet' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-purple-500"></i>
                            Additional Information
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Recorded By</p>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ payment?.paid_by_user?.full_name || 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Created At</p>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ payment?.created_at ? new Date(payment.created_at).toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Last Updated</p>
                                <p class="text-base text-gray-900 dark:text-white">
                                    {{ payment?.updated_at ? new Date(payment.updated_at).toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A' }}
                                </p>
                            </div>
                            <div v-if="payment?.notes">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Notes</p>
                                <p class="text-base text-gray-900 dark:text-white whitespace-pre-wrap bg-gray-50 dark:bg-gray-700/50 p-3 rounded">
                                    {{ payment.notes }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div v-if="paymentHistory && paymentHistory.length > 0" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-history text-indigo-500"></i>
                        Payment History for This Fee
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Amount</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Receipt</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr
                                    v-for="history in paymentHistory"
                                    :key="history.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                >
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ history.created_at ? new Date(history.created_at).toLocaleDateString() : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ formatCurrency(history.amount_paid) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span :class="[
                                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                            history.status === 'paid' 
                                                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                                : history.status === 'partial'
                                                ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300'
                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
                                        ]">
                                            {{ history.status || 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        <span v-if="history.receipt_number" class="font-mono">
                                            {{ history.receipt_number }}
                                        </span>
                                        <span v-else class="text-gray-400 dark:text-gray-500">N/A</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AdminPageWrapper>
    </App>
</template>

