<script setup lang="ts">
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface ClientData {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    group_id: number | null;
    is_active: boolean;
}

interface Group {
    id: number;
    name: string;
}

const props = defineProps<{
    client: ClientData | null;
    groups: Group[];
}>();

const isEditing = computed(() => !!props.client);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Clients', href: '/admin/clients' },
    { title: isEditing.value ? `Edit ${props.client!.name}` : 'New Client', href: '#' },
];

const form = useForm({
    name: props.client?.name ?? '',
    email: props.client?.email ?? '',
    phone: props.client?.phone ?? '',
    group_id: props.client?.group_id ?? '',
    password: '',
    is_active: props.client?.is_active ?? true,
});

function submit() {
    if (isEditing.value) {
        form.put(`/admin/api/clients/${props.client!.id}`);
    } else {
        form.post('/admin/api/clients');
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <div class="max-w-lg">
                <!-- Header -->
                <div class="flex items-center gap-4">
                    <Link href="/admin/clients" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <ArrowLeft class="h-5 w-5" />
                    </Link>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ isEditing ? 'Edit Client' : 'New Client' }}
                    </h1>
                </div>

                <!-- Form -->
                <div class="mt-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name *</label>
                        <input
                            v-model="form.name"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email *</label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input
                            v-model="form.phone"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Group</label>
                        <select
                            v-model="form.group_id"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                        >
                            <option value="">No group</option>
                            <option v-for="g in groups" :key="g.id" :value="g.id">{{ g.name }}</option>
                        </select>
                        <p v-if="form.errors.group_id" class="mt-1 text-xs text-red-500">{{ form.errors.group_id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ isEditing ? 'New Password (leave empty to keep current)' : 'Password (leave empty for random)' }}
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                        />
                    </div>

                    <div v-if="isEditing" class="flex items-center gap-2">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-400"
                        />
                        <label class="text-sm font-medium text-gray-700">Active</label>
                    </div>

                    <div class="flex gap-3 border-t border-gray-100 pt-6">
                        <button
                            class="rounded-lg bg-gray-900 px-6 py-2.5 text-sm font-medium text-white hover:bg-gray-800 disabled:opacity-50"
                            :disabled="form.processing"
                            @click="submit"
                        >
                            {{ form.processing ? 'Saving...' : (isEditing ? 'Update Client' : 'Create Client') }}
                        </button>
                        <Link
                            href="/admin/clients"
                            class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50"
                        >
                            Cancel
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>