<script setup lang="ts">
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { Plus, Search, Eye, Pencil, UserCheck, UserX, Upload } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Clients', href: '/admin/clients' },
];

interface Client {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    group_name: string | null;
    group_id: number | null;
    is_active: boolean;
    orders_count: number;
}

interface Group {
    id: number;
    name: string;
}

const props = defineProps<{
    clients: {
        data: Client[];
        links: any[];
        current_page: number;
        last_page: number;
    };
    groups: Group[];
    filters: { search?: string; group_id?: string };
}>();

const search = ref(props.filters.search ?? '');
const selectedGroup = ref(props.filters.group_id ?? '');
const showImport = ref(false);
const csvFile = ref<File | null>(null);
const importErrors = ref<Record<string, string>>({});

let timeout: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(timeout);
    timeout = setTimeout(applyFilters, 400);
});

watch(selectedGroup, applyFilters);

function applyFilters() {
    router.get('/admin/clients', {
        search: search.value || undefined,
        group_id: selectedGroup.value || undefined,
    }, { preserveState: true });
}

function toggleActive(client: Client) {
    router.post(`/admin/api/clients/${client.id}/toggle-active`, {}, { preserveScroll: true });
}

function handleCsvSelect(e: Event) {
    const input = e.target as HTMLInputElement;
    csvFile.value = input.files?.[0] ?? null;
}

function importCsv() {
    if (!csvFile.value) return;
    const formData = new FormData();
    formData.append('csv_file', csvFile.value);
    router.post('/admin/api/clients/import-csv', formData, {
        forceFormData: true,
        onError: (errs) => { importErrors.value = errs; },
        onSuccess: () => { showImport.value = false; csvFile.value = null; },
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Clients</h1>
                <div class="flex gap-2">
                    <button
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        @click="showImport = !showImport"
                    >
                        <Upload class="h-4 w-4" />
                        Import CSV
                    </button>
                    <Link
                        href="/admin/clients/create"
                        class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-800"
                    >
                        <Plus class="h-4 w-4" />
                        Add Client
                    </Link>
                </div>
            </div>

            <!-- CSV Import Panel -->
            <div v-if="showImport" class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                <h3 class="text-sm font-semibold text-blue-900">Import Clients from CSV</h3>
                <p class="mt-1 text-xs text-blue-700">
                    Required columns: <strong>name</strong>, <strong>email</strong>.
                    Optional: <strong>group</strong>, <strong>phone</strong>, <strong>password</strong>.
                    Existing emails will be skipped.
                </p>
                <div class="mt-3 flex items-center gap-3">
                    <input type="file" accept=".csv,.txt" class="text-sm text-gray-600" @change="handleCsvSelect" />
                    <button
                        class="rounded-lg bg-blue-600 px-4 py-1.5 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                        :disabled="!csvFile"
                        @click="importCsv"
                    >
                        Upload & Import
                    </button>
                </div>
                <p v-if="importErrors.csv_file" class="mt-2 text-xs text-red-600">{{ importErrors.csv_file }}</p>
            </div>

            <!-- Filters -->
            <div class="flex gap-3">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search name, email, phone..."
                        class="w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-4 text-sm shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                    />
                </div>
                <select
                    v-model="selectedGroup"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
                >
                    <option value="">All Groups</option>
                    <option v-for="g in groups" :key="g.id" :value="g.id">{{ g.name }}</option>
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                <table class="w-full min-w-175 text-left text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-600">Name</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Email</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Phone</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Group</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600">Orders</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="client in clients.data" :key="client.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ client.name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ client.email }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ client.phone || '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ client.group_name || '—' }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">{{ client.orders_count }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                    :class="client.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                                >
                                    {{ client.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <Link
                                        :href="`/admin/clients/${client.id}`"
                                        class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-blue-600"
                                        title="View"
                                    >
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                    <Link
                                        :href="`/admin/clients/${client.id}/edit`"
                                        class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                                        title="Edit"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                    <button
                                        class="rounded p-1.5 text-gray-400 hover:bg-gray-100"
                                        :title="client.is_active ? 'Deactivate' : 'Activate'"
                                        @click="toggleActive(client)"
                                    >
                                        <component
                                            :is="client.is_active ? UserX : UserCheck"
                                            class="h-4 w-4"
                                            :class="client.is_active ? 'hover:text-red-500' : 'hover:text-green-600'"
                                        />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="clients.data.length === 0">
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">No clients found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="clients.last_page > 1" class="flex gap-1">
                <Link
                    v-for="link in clients.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    class="rounded-lg border px-3 py-1.5 text-sm"
                    :class="link.active ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                    v-html="link.label"
                />
            </div>
        </div>
    </AppLayout>
</template>