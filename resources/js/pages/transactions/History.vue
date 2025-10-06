<script setup lang="ts">
import { onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    type BreadcrumbItem,
    type PaginatedResponse,
    type Transaction,
    type User,
} from '@/types';
import { Head, usePage, router } from '@inertiajs/vue3';
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

interface Props {
    user: User;
    transactions: PaginatedResponse<Transaction>;
}

const props = defineProps<Props>();

// Define breadcrumbs for the layout
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Transaction History', href: '/' },
];

// Get the Inertia page context
const page = usePage();

// Helper function to update flash message
const updateMessage = (message: string) => {
    page.props.flash.message = message;
};

// Initialize Pusher connection when the component is mounted
onMounted(() => {
    const pusher = new Pusher('acc38bedfa1b83343f52', {
        cluster: 'eu',
    });

    const channel = pusher.subscribe('mini-wallet-notifications');

    // Listen for "transfer-saved" event from the server
    channel.bind('transfer-saved', (data: any) => {
        if (data.message?.success && data.message.receiver_id === props.user.id) {
            updateMessage(`You received ${data.message.amount} €`);
            // Reload only the specified props instead of the whole page
            router.reload({ only: ['transactions', 'user'] });
        }
    });
});
</script>

<template>

    <Head title="Transaction History" />

    <AppLayout :breadcrumbs="breadcrumbs" :user="user">
        <!-- Flash notification area -->
        <div v-if="page.props.flash?.message" class="p-4 mb-4">
            <Alert class="bg-blue-200">
                <Info class="h-4 w-4" />
                <AlertTitle>Notification</AlertTitle>
                <AlertDescription>
                    {{ page.props.flash.message }}
                </AlertDescription>
            </Alert>
        </div>

        <!-- Transactions table -->
        <div class="p-4">
            <Table>
                <TableCaption>A list of all Transaction History</TableCaption>

                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]">ID</TableHead>
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

                        <!-- If the current user is the sender, show "-" before the amount -->
                        <TableCell>
                            <span v-if="transaction.sender.id === user.id">-</span>
                            {{ transaction.amount }}
                        </TableCell>

                        <TableCell>{{ transaction.commission_fee }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Pagination component -->
            <Pagination :resource="transactions" />
        </div>
    </AppLayout>
</template>
