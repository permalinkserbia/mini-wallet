<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type PaginatedResponse, type Transaction } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Info } from 'lucide-vue-next';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import Pagination from '@/components/Pagination.vue';
import Pusher from 'pusher-js';
import type { User } from '@/types';
import { router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transaction History',
        href: '/',
    },
];

const page = usePage();

interface Props {
    user: User;
    transactions: PaginatedResponse<Transaction>
}

const props = defineProps<Props>();

// Enable pusher logging - don't include this in production
Pusher.logToConsole = true;

var pusher = new Pusher('acc38bedfa1b83343f52', {
    cluster: 'eu'
});

const updateMessage = (message) => {
    page.props.flash.message = message
}

var channel = pusher.subscribe('mini-wallet-notifications');

channel.bind('transfer-saved', function (data) {
    if (data.message.success == true && data.message.receiver_id == props.user.id) {
        updateMessage('You received ' + data.message.amount + ' €');
        router.reload({ only: ['transactions', 'user'] });
    }
});

</script>

<template>

    <Head title="Associate Types" />

    <AppLayout :breadcrumbs="breadcrumbs" :user="user">
        <div class="p-4">
            <div v-if="page.props.flash?.message" class="alert mb-4">
                <Alert class="bg-blue-200">
                    <Info class="h-4 w-4" />
                    <AlertTitle>Notification!</AlertTitle>
                    <AlertDescription>
                        {{ page.props.flash.message }}
                    </AlertDescription>
                </Alert>
            </div>
        </div>
        <div>
            <Table>
                <TableCaption>A list of all Transaction History.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]">
                            ID
                        </TableHead>
                        <TableHead>Sender</TableHead>
                        <TableHead>Receiver</TableHead>
                        <TableHead>Amount</TableHead>
                        <TableHead>Commission Fee</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="transaction in transactions.data" :key="transaction.id">
                        <TableCell>{{ transaction.id }}</TableCell>
                        <TableCell>{{ transaction.sender.name }}</TableCell>
                        <TableCell>{{ transaction.receiver.name }}</TableCell>
                        <TableCell>{{ transaction.amount }}</TableCell>
                        <TableCell>{{ transaction.commission_fee }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <Pagination :resource="transactions" />
        </div>
    </AppLayout>
</template>
