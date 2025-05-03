<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard Administrateur" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard Administrateur
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium">Bienvenue, Admin!</h3>
                    <p>Ceci est le tableau de bord administrateur avec des fonctionnalités spéciales.</p>

                    <!-- Contenu spécifique au dashboard administrateur -->
                </div>
            </div>
        </div>
    </AppLayout>
</template>
