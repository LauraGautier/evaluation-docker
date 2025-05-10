<template>
    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center space-y-4">
                <!-- Liens de navigation -->
                <nav class="flex flex-wrap justify-center gap-x-8 gap-y-2">
                    <Link :href="route('terms.show')" class="text-gray-600 hover:text-gray-900">
                        Conditions d'utilisation
                    </Link>
                    <Link :href="route('policy.show')" class="text-gray-600 hover:text-gray-900">
                        Politique de confidentialité
                    </Link>
                </nav>

                <!-- Copyright -->
                <div class="text-center text-sm text-gray-500">
                    <p>&copy; {{ currentYear }} {{ appName }}. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const currentYear = computed(() => new Date().getFullYear());
const appName = computed(() => import.meta.env.VITE_APP_NAME || 'Mon Application');
</script>
