<script setup lang="ts">
import { Label } from '@/components/ui/label'
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import {
  NumberField,
  NumberFieldContent,
  NumberFieldDecrement,
  NumberFieldIncrement,
  NumberFieldInput,
} from "@/components/ui/number-field"
import Button from '@/components/ui/button/Button.vue';
import { useForm } from '@inertiajs/vue3';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'New Transfer',
        href: '/transfer',
    },
];

const form = useForm({
    receiver_id: '',
    amount: ''
});


interface User {
    id: number,
    name: string,
}

interface Props {
    users: User[]
}

const props = defineProps<Props>();

const handleSubmit = () => {
    form.post('/transfer');
}

</script>

<template>
    <Head title="New Transfer" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <form @submit.prevent="handleSubmit" class="w-8/12 space-y-4" action="/transfer">
                <div class="space-y-2">
                    <Label for="Receiver">Receiver</Label>
                    <Select class="w-[100%]" v-model="form.receiver_id">
                        <SelectTrigger class="w-[100%]">
                            <SelectValue placeholder="Receiver" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="user in props.users" :value="user.id">
                                {{ user.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <div v-if="form.errors.receiver_id" class="text-sm text-red-600">{{ form.errors.receiver_id }}</div>
                </div>
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
                    <div v-if="form.errors.amount" class="text-sm text-red-600">{{ form.errors.amount }}</div>
                </div>
                <Button type="submit" :disabled="form.processing">Create</Button>
            </form>
        </div>
    </AppLayout>
</template>
