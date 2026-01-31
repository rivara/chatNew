<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import axios from 'axios'

axios.defaults.withCredentials = true

const token = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement
if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}



const nick = ref('')
const roomId = ref('general') // Sala por defecto

const enterChat = () => {
    if (!nick.value.trim() || !roomId.value.trim()) return

    router.visit('/chat', {
        data: {
            nick: nick.value,
            roomId: roomId.value
        }
    })
}
</script>

<template>
    <Head title="Welcome" />

    <div class="flex min-h-screen items-center justify-center bg-[#FDFDFC] dark:bg-[#0a0a0a]">
        <div class="w-full max-w-sm rounded-lg bg-white p-6 shadow dark:bg-[#161615]">
            <h1 class="mb-4 text-lg font-semibold dark:text-white">
                Elige tu nick y sala
            </h1>

            <input
                v-model="nick"
                type="text"
                placeholder="Tu nick..."
                class="w-full rounded-md border p-3 text-sm
                       dark:border-gray-600 dark:bg-[#0f0f0f] dark:text-white"
            />

            <input
                v-model="roomId"
                type="text"
                placeholder="Nombre de sala..."
                class="w-full rounded-md border p-3 text-sm mt-2
                       dark:border-gray-600 dark:bg-[#0f0f0f] dark:text-white"
            />

            <button
                @click="enterChat"
                class="mt-4 w-full rounded-md bg-black py-2 text-white
                       dark:bg-white dark:text-black">
                Entrar al chat
            </button>
        </div>
    </div>
</template>
