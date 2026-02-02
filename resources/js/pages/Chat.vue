<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import axios from 'axios'

declare global {
    interface Window {
        Echo: Echo<any>
        Pusher: typeof Pusher
    }
}

window.Pusher = Pusher

const props = defineProps<{
    nick: string
    roomId: string
}>()

const message = ref('')
const messages = ref<{ user: string; text: string }[]>([])
const onlineUsers = ref<string[]>([])
const sending = ref(false)

let echoInstance: Echo<any> | null = null

const initEcho = () => {
    if (echoInstance) return

    echoInstance = new Echo({
        broadcaster: 'pusher',
        key: 'local',
        cluster: 'mt1',
        wsHost: window.location.hostname,
        wsPort: 6001,
        wssPort: 6001,
        forceTLS: false,
        enabledTransports: ['ws'],
        disableStats: true,
    })

    const channelName = `chat.${props.roomId}`


    echoInstance.channel(`chat.${props.roomId}`)
        .listen('.MessageSent', (e: any) => {
            messages.value.push({
                user: e.nick,
                text: e.message,
            })
        })


}



onMounted(() => {
    // Debug para confirmar props
    console.log('Chat montado → nick:', props.nick, 'roomId:', props.roomId)

    if (!props.roomId) {
        console.error('roomId no recibido → no se puede conectar')
        alert('Error: No se recibió el ID de la sala')
        return
    }

    initEcho()

    // Añadimos nuestro nick de inmediato (por si .here llega vacío)
    if (props.nick && !onlineUsers.value.includes(props.nick)) {
        onlineUsers.value.push(props.nick)
    }
})

onUnmounted(() => {
    if (echoInstance) {
        echoInstance.leave(`chat.${props.roomId}`)
        echoInstance.disconnect()
        echoInstance = null
    }

    onlineUsers.value = []
    messages.value = []
})

const sendMessage = async () => {
    if (!message.value.trim()) return

    sending.value = true
    const text = message.value.trim()

    const payload = {
        nick: props.nick,
        message: text,
        roomId: props.roomId,   // ← snake_case, coincide con validación backend
    }

    console.log('Enviando mensaje → payload:', payload)

    try {
        await axios.post('/messages', payload)

        // Añadimos localmente
        messages.value.push({
            user: props.nick,
            text,
        })

        message.value = ''
    } catch (error: any) {
        console.error('Error enviando mensaje:', error)
        if (error.response?.data) {
            console.log('Respuesta backend:', error.response.data)
            const msg = error.response.data.message || 'Validación fallida'
            alert(`Error: ${msg}`)
        } else {
            alert('No se pudo conectar al servidor')
        }
    } finally {
        sending.value = false
    }
}
</script>

<template>
    <div class="flex h-screen bg-gray-50">
        <aside class="w-64 bg-white border-r shadow-sm p-4 overflow-y-auto">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">Conectados</h2>
            <ul class="space-y-2 text-sm">
                <li v-for="user in onlineUsers" :key="user"
                    class="flex items-center gap-2 px-3 py-2 rounded hover:bg-red-100">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    {{ user }}
                </li>
                <li v-if="!onlineUsers.length" class="text-gray-500 italic">
                    Nadie conectado aún
                </li>
            </ul>
        </aside>

        <main class="flex-1 flex flex-col">
            <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50">
                <div v-for="(msg, index) in messages" :key="index" :class="[
                    'max-w-[70%] p-3 rounded-lg shadow-sm',
                    msg.user === props.nick
                        ? 'ml-auto bg-indigo-600 text-white'
                        : 'bg-pink-500 text-gray-900'
                ]">
                    <div class="font-medium text-sm opacity-90">{{ msg.user }}</div>
                    <div class="mt-1">{{ msg.text }}</div>
                </div>
            </div>

            <div class="p-4 border-t bg-white flex items-center gap-3">
                <input v-model="message" @keyup.enter="sendMessage"
                    class="flex-1 border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="Escribe un mensaje..." :disabled="sending" />
                <button @click="sendMessage" :disabled="sending || !message.trim()"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 disabled:opacity-50 transition">
                    {{ sending ? 'Enviando...' : 'Enviar' }}
                </button>
            </div>
        </main>
    </div>
</template>
