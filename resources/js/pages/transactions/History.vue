<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transaction History',
        href: '/',
    },
];

const page = usePage();

interface Transaction {
    id: number,
    sender: object,
    receiver: object,
    amount: string,
    commission_fee: string,
}

interface Props {
    transactions: Transaction[]
}

const props = defineProps<Props>();

</script>

<template>

    <Head title="Associate Types" />

    <AppLayout :breadcrumbs="breadcrumbs">
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
                    <TableRow v-for="transaction in transactions" :key="transaction.id">
                        <TableCell>{{ transaction.id }}</TableCell>
                        <TableCell>{{ transaction.sender.name }}</TableCell>
                        <TableCell>{{ transaction.receiver.name }}</TableCell>
                        <TableCell>{{ transaction.amount }}</TableCell>
                        <TableCell>{{ transaction.commission_fee }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
