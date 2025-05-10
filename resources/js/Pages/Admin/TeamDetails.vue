<template>
    <AppLayout :title="`Équipe: ${team.name}`">
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Détails de l'équipe: {{ team.name }}
          </h2>
          <Link :href="route('admin.teams.list')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
          </Link>
        </div>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:items-stretch">
            <!-- Informations de l'équipe -->
            <div class="lg:col-span-1 flex">
              <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 w-full flex flex-col">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                  Informations de l'équipe
                </h3>

                <div class="space-y-4 flex-grow">
                  <div>
                    <div class="text-sm font-medium text-gray-500">Nom:</div>
                    <div class="mt-1 text-sm text-gray-900">{{ team.name }}</div>
                  </div>

                  <div>
                    <div class="text-sm font-medium text-gray-500">Propriétaire:</div>
                    <div class="mt-1 text-sm text-gray-900 flex items-center">
                      <div class="h-8 w-8 rounded-full overflow-hidden bg-gray-100 mr-2">
                        <img v-if="team.owner && team.owner.profile_photo_url" :src="team.owner.profile_photo_url" :alt="team.owner.name" class="h-full w-full object-cover">
                        <div v-else class="h-full w-full flex items-center justify-center bg-blue-200 text-blue-600">
                          <span class="text-sm font-bold">{{ team.owner ? team.owner.name.charAt(0) : '?' }}</span>
                        </div>
                      </div>
                      <div>
                        {{ team.owner ? team.owner.name : 'Non spécifié' }}
                        <div class="text-xs text-gray-500">{{ team.owner ? team.owner.email : '' }}</div>
                      </div>
                    </div>
                  </div>

                  <div>
                    <div class="text-sm font-medium text-gray-500">Date de création:</div>
                    <div class="mt-1 text-sm text-gray-900">{{ formatDate(team.created_at) }}</div>
                  </div>
                </div>

                <div class="mt-6 border-t pt-4">
                  <div class="space-y-3">
                    <button
                      v-if="!team.personal_team"
                      type="button"
                      class="inline-flex w-full items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition"
                      @click="confirmTeamDeletion"
                    >
                      <i class="fas fa-trash-alt mr-2"></i> Supprimer l'équipe
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Membres de l'équipe -->
            <div class="lg:col-span-2 flex">
              <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 w-full">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-medium text-gray-900">
                    Membres de l'équipe
                  </h3>
                  <button
                    type="button"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
                    @click="addTeamMember"
                  >
                    <i class="fas fa-user-plus mr-2"></i> Ajouter un membre
                  </button>
                </div>

                <div class="overflow-x-auto bg-gray-50 rounded-lg">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                      <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                        <th scope="col" class="relative px-6 py-3">
                          <span class="sr-only">Actions</span>
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-if="members.length === 0">
                        <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                          Aucun membre dans cette équipe.
                        </td>
                      </tr>
                      <tr v-for="member in members" :key="member.id" :class="{'bg-gray-50': member.id === team.owner?.id}">
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden bg-gray-100">
                              <img v-if="member.profile_photo_url" :src="member.profile_photo_url" :alt="member.name" class="h-full w-full object-cover">
                              <div v-else class="h-full w-full flex items-center justify-center bg-blue-200 text-blue-600">
                                <span class="font-bold">{{ member.name.charAt(0) }}</span>
                              </div>
                            </div>
                            <div class="ml-4">
                              <div class="text-sm font-medium text-gray-900">
                                {{ member.name }}
                                <span v-if="member.id === team.owner?.id" class="ml-2 text-xs text-gray-500">(Propriétaire)</span>
                              </div>
                              <div class="text-sm text-gray-500">{{ member.email }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                          <button
                            type="button"
                            class="text-blue-600 hover:text-blue-900 mr-3"
                            @click="editTeamMember(member)"
                          >
                            Modifier
                          </button>

                          <button
                            v-if="member.id !== team.owner?.id"
                            type="button"
                            class="text-red-600 hover:text-red-900"
                            @click="confirmRemoveMember(member)"
                          >
                            Retirer
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal de suppression d'équipe -->
      <div v-if="confirmingTeamDelete" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-3">
            Supprimer l'équipe
          </h2>

          <p class="mb-3 text-sm text-gray-600">
            Êtes-vous sûr de vouloir supprimer l'équipe <span class="font-semibold">{{ team.name }}</span> ?
          </p>

          <p class="mb-6 text-sm text-red-600">
            Cette action est irréversible. Tous les projets, tâches et données associés à cette équipe seront supprimés.
          </p>

          <div class="flex justify-end">
            <button
              type="button"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
              @click="confirmingTeamDelete = false"
            >
              Annuler
            </button>

            <button
              type="button"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              :disabled="processing"
              @click="deleteTeam"
            >
              <i class="fas fa-trash-alt mr-2"></i> Supprimer
            </button>
          </div>
        </div>
      </div>

      <!-- Modal de retrait de membre -->
      <div v-if="memberToRemove" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-3">
            Retirer le membre
          </h2>

          <p class="mb-3 text-sm text-gray-600">
            Êtes-vous sûr de vouloir retirer <span class="font-semibold">{{ memberToRemove.name }}</span> de l'équipe ?
          </p>

          <div class="flex justify-end">
            <button
              type="button"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
              @click="memberToRemove = null"
            >
              Annuler
            </button>

            <button
              type="button"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              :disabled="processing"
              @click="removeMember"
            >
              <i class="fas fa-user-minus mr-2"></i> Retirer
            </button>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>

  <script>
  import { defineComponent } from 'vue'
  import { Link, router } from '@inertiajs/vue3'
  import AppLayout from '@/Layouts/AppLayout.vue'

  export default defineComponent({
    components: {
      AppLayout,
      Link
    },

    props: {
      team: Object,
      members: Array,
      projects: Array
    },

    data() {
      return {
        confirmingTeamDelete: false,
        memberToRemove: null,
        processing: false
      }
    },

    methods: {
      formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('fr-FR', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric'
        }).format(date);
      },

      getRoleBadgeClass(role) {
        switch (role) {
          case 'administrateur':
            return 'bg-red-100 text-red-800';
          case 'manager':
            return 'bg-blue-100 text-blue-800';
          case 'collaborateur':
            return 'bg-green-100 text-green-800';
          default:
            return 'bg-gray-100 text-gray-800';
        }
      },

      getStatusBadgeClass(status) {
        switch (status) {
          case 'active':
            return 'bg-green-100 text-green-800';
          case 'completed':
            return 'bg-blue-100 text-blue-800';
          case 'on_hold':
            return 'bg-yellow-100 text-yellow-800';
          default:
            return 'bg-gray-100 text-gray-800';
        }
      },

      formatStatus(status) {
        switch (status) {
          case 'active':
            return 'Actif';
          case 'completed':
            return 'Terminé';
          case 'on_hold':
            return 'En pause';
          default:
            return status;
        }
      },

      confirmTeamDeletion() {
        this.confirmingTeamDelete = true;
      },

      deleteTeam() {
        this.processing = true;
        router.delete(route('admin.teams.destroy', this.team.id), {
          onSuccess: () => {
            // La redirection se fait automatiquement par le contrôleur
            this.processing = false;
          },
          onError: () => {
            this.processing = false;
            this.confirmingTeamDelete = false;
          }
        });
      },

      addTeamMember() {
        router.visit(route('admin.teams.members.create', this.team.id));
      },

      editTeamMember(member) {
        router.visit(route('admin.teams.members.edit', { team: this.team.id, user: member.id }));
      },

      confirmRemoveMember(member) {
        this.memberToRemove = member;
      },

      removeMember() {
        this.processing = true;
        router.delete(route('admin.teams.members.destroy', { team: this.team.id, user: this.memberToRemove.id }), {
          onSuccess: () => {
            this.processing = false;
            this.memberToRemove = null;
          },
          onError: () => {
            this.processing = false;
            this.memberToRemove = null;
          }
        });
      }
    }
  })
  </script>
