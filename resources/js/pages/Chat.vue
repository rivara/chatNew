<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import axios from 'axios'

// Tipado global
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

const echoReady = ref(false)
const sending = ref(false)

const initEcho = () => {
    if (window.Echo || echoReady.value) return
    echoReady.value = true

    console.log("[DEBUG] Paso 1: Iniciando new Echo...")

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: 'local',                     // ← clave obligatoria (aunque fake en local)
        cluster: 'mt1',                   // ← obligatorio para compatibilidad
        wsHost: window.location.hostname, // localhost
        wsPort: 6001,
        wssPort: 6001,
        forceTLS: false,
        enabledTransports: ['ws'],        // solo ws en local
        disableStats: true,
    })

    console.log("[DEBUG] Paso 2: Echo creado", window.Echo)

    const channelName = `chat.${props.roomId}`
    console.log("[DEBUG] Paso 3: Intentando suscribir a canal:", channelName)

    let channel
    try {
        channel = window.Echo.channel(channelName)
        console.log("[DEBUG] Paso 4: Canal obtenido", channel)
    } catch (err) {
        console.error("[DEBUG] ERROR al obtener canal:", err)
        return
    }

    console.log("[DEBUG] Paso 5: Registrando listeners...")

    channel.listen('.UserJoined', (e: { nick: string }) => {
        console.log("Evento UserJoined recibido:", e)
        if (!onlineUsers.value.includes(e.nick)) {
            onlineUsers.value.push(e.nick)
        }
    })

    channel.listen('.UserLeft', (e: { nick: string }) => {
        console.log("Evento UserLeft recibido:", e)
        onlineUsers.value = onlineUsers.value.filter(n => n !== e.nick)
    })

    console.log("[DEBUG] Paso 6: Listener UserJoined y UserLeft registrados")

    channel.listen('.MessageSent', (e: { nick: string; message: string }) => {
        console.log("Evento MessageSent recibido:", e)
        messages.value.push({
            user: e.nick,
            text: e.message,
        })
    })

    console.log("[DEBUG] Paso 7: Todos los listeners registrados")
    console.log("[DEBUG] Echo final:", window.Echo)
}

// onMounted(() => {
//     initEcho()

//     // Añadimos nick localmente desde el principio (para verte a ti mismo)
//     if (!onlineUsers.value.includes(props.nick)) {
 //         onlineUsers.value.push(props.nick)
//     }

//     axios.post('/chat/joined', {
//         nick: props.nick,
//         roomId: props.roomId,
//     })
//         .then(() => {
//             console.log("Joined OK - Notificado al servidor")
//         })
//         .catch(err => console.error("Joined FALLÓ:", err.response?.data || err))
// })


// onMounted(() => {
//     console.log("[DEBUG] props recibidos en chat.vue → nick:", props.nick, "roomId:", props.roomId)

//     if (!props.roomId) {
//         console.error("[ERROR] roomId no llegó al componente!")
//         alert("Error: No se recibió el ID de la sala")
//         return
//    }

//     initEcho()

//     if (!onlineUsers.value.includes(props.nick)) {
//         onlineUsers.value.push(props.nick)
//     }

//     axios.post('/chat/joined', {
//         nick: props.nick,
//         roomId: props.roomId,
//     })
//         .then(() => console.log("Joined OK"))
//         .catch(err => {
//             console.error("Joined FALLÓ:", err.response?.data || err)
//         })
// })


// onUnmounted(() => {
//     axios.post('/chat/left', {
//         nick: props.nick,
//         roomId: props.roomId,
//     })
//         .then(() => console.log("Left OK"))
//         .catch(err => console.error("Left FALLÓ:", err))

//     if (window.Echo) {
//         window.Echo.leave(`chat.${props.roomId}`)
//         console.log("Echo dejado del canal")
//     }
// })



onMounted(async () => {
    console.log(
        "[DEBUG] props recibidos → nick:",
        props.nick,
        "roomId:",
        props.roomId
    )

    if (!props.roomId) {
        console.error("[ERROR] roomId no llegó")
        return
    }

    initEcho()

    try {
        const res = await axios.post('/chat/joined', {
            nick: props.nick,
            roomId: props.roomId,
        })

        // 👇 LA CLAVE
        onlineUsers.value = res.data.onlineUsers
        console.log("Usuarios online:", onlineUsers.value)
    } catch (err: any) {
        console.error("Joined FALLÓ:", err.response?.data || err)
    }
})


onUnmounted(() => {
    axios.post('/chat/left', {
        nick: props.nick,
        roomId: props.roomId,
    }).catch(err => console.error("Left FALLÓ:", err))

    if (window.Echo) {
        window.Echo.leave(`chat.${props.roomId}`)
        console.log("Echo dejado del canal")
    }
})
















const sendMessage = async () => {
    if (!message.value.trim()) return

    sending.value = true

    const textToSend = message.value.trim()

    try {
        await axios.post('/messages', {
            nick: props.nick,
            message: textToSend,
            roomId: props.roomId,
        })

        // Mostrar localmente (porque .toOthers() no te lo devuelve a ti)
        messages.value.push({
            user: props.nick,
            text: textToSend,
        })

        message.value = ''
    } catch (error: any) {
        console.error('Error enviando mensaje:', error)
        let errorMsg = 'Error al enviar el mensaje'
        if (error.response?.data?.errors) {
            const errors = error.response.data.errors
            Object.values(errors).forEach((msgs: any) => {
                errorMsg += `\n${msgs[0]}`
            })
        }
        alert(errorMsg)
    } finally {
        sending.value = false
    }
}
</script>

<template>
  <div class="chat-container">
    <div class="online">
      <strong>Conectados:</strong>
      {{ onlineUsers.join(', ') || 'Nadie aún' }}
    </div>

    <div class="messages">
      <div v-for="(msg, i) in messages" :key="i" class="message">
        <strong>{{ msg.user }}:</strong> {{ msg.text }}
      </div>
    </div>

    <div class="input-area">
      <input
        v-model="message"
        @keyup.enter="sendMessage"
        placeholder="Escribe tu mensaje..."
        :disabled="sending"
      />
      <button @click="sendMessage" :disabled="sending || !message.trim()">
        {{ sending ? 'Enviando...' : 'Enviar' }}
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Tu CSS se mantiene igual */
</style>
