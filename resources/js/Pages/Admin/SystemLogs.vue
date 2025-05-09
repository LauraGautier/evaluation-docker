<template>
    <AppLayout title="Paramètres système">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Informations système
        </h2>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Informations système -->
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Détails de l'application</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-4">
                <div>
                  <div class="text-sm font-medium text-gray-500">Version de Laravel</div>
                  <div class="mt-1 text-sm text-gray-900">{{ systemInfo.laravelVersion }}</div>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500">Version de PHP</div>
                  <div class="mt-1 text-sm text-gray-900">{{ systemInfo.phpVersion }}</div>
                </div>
              </div>
              <div class="space-y-4">
                <div>
                  <div class="text-sm font-medium text-gray-500">Base de données</div>
                  <div class="mt-1 text-sm text-gray-900">{{ systemInfo.database }}</div>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500">Environnement</div>
                  <div class="mt-1">
                    <span
                      class="px-2 py-1 text-xs rounded-full"
                      :class="getEnvironmentClass(systemInfo.environment)"
                    >
                      {{ systemInfo.environment }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>

  <script>
  import { defineComponent } from 'vue'
  import AppLayout from '@/Layouts/AppLayout.vue'

  export default defineComponent({
    components: {
      AppLayout
    },

    props: {
      systemInfo: {
        type: Object,
        default: () => ({
          laravelVersion: 'Laravel 12.x',
          phpVersion: 'PHP 8.4.3',
          environment: 'local',
          database: 'mysql'
        })
      },
      maintenance: {
        type: Object,
        default: () => ({
          enabled: false,
          since: null
        })
      }
    },

    data() {
      return {
        showConfirmModal: false,
        processing: false
      }
    },

    methods: {
      getEnvironmentClass(env) {
        switch (env) {
          case 'production':
            return 'bg-green-100 text-green-800';
          case 'staging':
            return 'bg-blue-100 text-blue-800';
          case 'testing':
            return 'bg-yellow-100 text-yellow-800';
          case 'local':
            return 'bg-gray-100 text-gray-800';
          default:
            return 'bg-gray-100 text-gray-800';
        }
      },
    }
  })
  </script>
