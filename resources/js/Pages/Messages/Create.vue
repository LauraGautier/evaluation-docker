<template>
    <AppLayout title="Nouveau message">
        <template #header>
            <div class="flex items-center">
                <Link :href="route('messages.index')" class="text-gray-600 hover:text-gray-900 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Nouveau message
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="mb-4">
                            <label for="recipient" class="block text-sm font-medium text-gray-700 mb-2">
                                Destinataire
                            </label>
                            <select
                                id="recipient"
                                v-model="form.recipient_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                :class="{ 'border-red-500': form.errors.recipient_id }"
                                required
                            >
                                <option value="">Sélectionnez un destinataire</option>
                                <option
                                    v-for="user in users"
                                    :key="user.id"
                                    :value="user.id"
                                >
                                    {{ user.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.recipient_id" class="mt-1 text-sm text-red-600">
                                {{ form.errors.recipient_id }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                Sujet (optionnel)
                            </label>
                            <input
                                id="subject"
                                v-model="form.subject"
                                type="text"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                :class="{ 'border-red-500': form.errors.subject }"
                            >
                            <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">
                                {{ form.errors.subject }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Message
                            </label>
                            <textarea
                                id="content"
                                v-model="form.content"
                                rows="5"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                :class="{ 'border-red-500': form.errors.content }"
                                required
                            ></textarea>
                            <p v-if="form.errors.content" class="mt-1 text-sm text-red-600">
                                {{ form.errors.content }}
                            </p>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <Link :href="route('messages.index')" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                                Annuler
                            </Link>
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Envoi en cours...
                                </span>
                                <span v-else class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Envoyer
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    users: Array,
    selectedUser: Object
});

const form = useForm({
    recipient_id: props.selectedUser?.id || '',
    subject: '',
    content: ''
});

const submit = () => {
    form.post(route('messages.store'));
};

onMounted(() => {
    if (props.selectedUser) {
        form.recipient_id = props.selectedUser.id;
    }
});
</script>
