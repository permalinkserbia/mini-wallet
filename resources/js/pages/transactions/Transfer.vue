<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import Button from '@/components/ui/button/Button.vue';
import {
    NumberField,
    NumberFieldContent,
    NumberFieldDecrement,
    NumberFieldIncrement,
    NumberFieldInput,
} from '@/components/ui/number-field';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Info } from 'lucide-vue-next';
import { type BreadcrumbItem, type User } from '@/types';

// Define props interface
interface Props {
    user: User;
    users: User[];
}

const props = defineProps<Props>();

// Define breadcrumbs for the layout
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'New Transfer', href: '/transfer' },
];

// Access Inertia page context
const page = usePage();

// Create Inertia form
const form = useForm({
    receiver_id: '',
    amount: '',
});

// Handle form submission
const handleSubmit = () => {
    form.post('/transfer');
};
</script>

<template>
    <Head title="New Transfer" />

    <AppLayout :breadcrumbs="breadcrumbs" :user="user">
        <!-- Flash message area -->
        <div v-if="page.props.flash?.message" class="p-4 mb-4">
            <Alert class="bg-red-200" variant="destructive">
                <Info class="h-4 w-4" />
                <AlertTitle>Error</AlertTitle>
                <AlertDescription>
                    {{ page.props.flash.message }}
                </AlertDescription>
            </Alert>
        </div>

        <!-- Transfer form -->
        <div class="p-4">
            <form @submit.prevent="handleSubmit" class="w-8/12 space-y-4">
                <!-- Receiver select -->
                <div class="space-y-2">
                    <Label for="Receiver">Receiver</Label>
                    <Select v-model="form.receiver_id" class="w-full">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select a receiver" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="userItem in props.users"
                                :key="userItem.id"
                                :value="userItem.id"
                            >
                                {{ userItem.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <div
                        v-if="form.errors.receiver_id"
                        class="text-sm text-red-600"
                    >
                        {{ form.errors.receiver_id }}
                    </div>
                </div>

                <!-- Amount input -->
                <div class="space-y-2">
                    <Label for="Amount">Amount</Label>
                    <NumberField
                        id="Amount"
                        v-model="form.amount"
                        :default-value="0"
                        :format-options="{
                            style: 'currency',
                            currency: 'EUR',
                            currencyDisplay: 'code',
                            currencySign: 'accounting',
                        }"
                    >
                        <NumberFieldContent>
                            <NumberFieldDecrement />
                            <NumberFieldInput />
                            <NumberFieldIncrement />
                        </NumberFieldContent>
                    </NumberField>
                    <div
                        v-if="form.errors.amount"
                        class="text-sm text-red-600"
                    >
                        {{ form.errors.amount }}
                    </div>
                </div>

                <!-- Submit button -->
                <Button type="submit" :disabled="form.processing">
                    Send
                </Button>
            </form>
        </div>
    </AppLayout>
</template>
