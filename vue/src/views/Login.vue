<template>
    <div class="min-h-screen flex items-center justify-center">
        <Card class="w-full max-w-md p-6">
            <CardHeader>
                <CardTitle>Logga in</CardTitle>
            </CardHeader>
            <CardContent class="flex flex-col gap-4">
                <Input v-model="email" type="email" placeholder="E-post" />
                <Input v-model="password" type="password" placeholder="Lösenord" @keydown.enter="login" />
                <Button @click="login">Logga in</Button>
                <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>
            </CardContent>
        </Card>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { useRouter } from 'vue-router'
import { apiFetch } from '@/lib/api.js'
const router = useRouter()

const email = ref('')
const password = ref('')
const error = ref('')

async function login() {
    try {
        const response = await apiFetch('/api/login', {
            method: 'POST',
            body: JSON.stringify({ email: email.value, password: password.value })
        })

        const data = await response.json()

        if (!response.ok) {
            error.value = 'Fel email eller lösenord'
            return
        }

        localStorage.setItem('token', data.token)
        router.push('/dashboard')
    } catch (e) {
        error.value = 'Något gick fel'
    }
}
</script>