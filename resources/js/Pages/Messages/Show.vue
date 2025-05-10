<template>
    <AppLayout title="Conversation">
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <Link :href="route('messages.index')" class="text-gray-600 hover:text-gray-900 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </Link>
                    <img
                        :src="otherUser.profile_photo_url"
                        :alt="otherUser.name"
                        class="h-10 w-10 rounded-full mr-3"
                    >
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ otherUser.name }}
                    </h2>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="flex flex-col h-96">
                        <!-- Messages area -->
                        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
                            <div v-for="message in messages" :key="message.id"
                                 :class="[
                                     'flex',
                                     message.sender_id === $page.props.auth.user.id ? 'justify-end' : 'justify-start'
                                 ]">
                                <div :class="[
                                    'max-w-sm px-4 py-2 rounded-lg',
                                    message.sender_id === $page.props.auth.user.id
                                        ? 'bg-gray-300 text-gray-900'
                                        : 'bg-gray-100 text-gray-900'
                                ]">
                                    <p class="text-sm">{{ message.content }}</p>
                                    <p :class="[
                                        'text-xs mt-1',
                                        message.sender_id === $page.props.auth.user.id
                                            ? 'text-blue-100'
                                            : 'text-gray-500'
                                    ]">
                                        <time-ago :datetime="message.created_at" />
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Message input -->
                        <div class="border-t border-gray-200 px-4 py-3">
                            <form @submit.prevent="sendMessage" class="flex space-x-2">
                                <input
                                    v-model="form.content"
                                    type="text"
                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    placeholder="Tapez votre message..."
                                    :disabled="form.processing"
                                >
                                <button
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                                    :disabled="form.processing || !form.content.trim()"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import TimeAgo from '@/Components/TimeAgo.vue';

const props = defineProps({
    conversation: Object,
    messages: Array,
    otherUser: Object
});

const form = useForm({
    content: ''
});

const messagesContainer = ref(null);

const sendMessage = () => {
    form.post(route('messages.reply', props.conversation.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            scrollToBottom();
        }
    });
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

onMounted(() => {
    scrollToBottom();
});
</script>
