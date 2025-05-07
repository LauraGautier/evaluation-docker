<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard Administrateur" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard Administrateur
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Bienvenue, Admin!</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Ceci est le tableau de bord administrateur avec des fonctionnalités spéciales.
                    </p>

                    <!-- Contenu spécifique au dashboard administrateur -->
                </div>
            </div>
        </div>
    </AppLayout>
</template>
