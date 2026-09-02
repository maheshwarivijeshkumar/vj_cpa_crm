<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { User, Save, Camera } from '@lucide/vue'
import AppLayout from '@/layouts/AppLayout.vue'

interface AuthUser {
    id: number
    first_name: string
    last_name: string
    email: string
    phone?: string
    timezone?: string
    locale?: string
    avatar_url?: string
}

const props = defineProps<{ user: AuthUser }>()

const form = useForm({
    first_name: props.user.first_name ?? '',
    last_name:  props.user.last_name ?? '',
    email:      props.user.email ?? '',
    phone:      props.user.phone ?? '',
    timezone:   props.user.timezone ?? '',
    locale:     props.user.locale ?? '',
    avatar:     null as File | null,
})

function onAvatarChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (file) form.avatar = file
}

function submit() {
    form.patch('/settings/profile', { forceFormData: true })
}
</script>

<template>
    <AppLayout title="Profile Settings">
        <div class="max-w-2xl mx-auto space-y-6">

            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 rounded-xl bg-cpa-very-light flex items-center justify-center">
                    <User :size="18" class="text-cpa-medium-dark" />
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-cpa-text-primary">Profile Settings</h1>
                    <p class="text-xs text-cpa-text-muted">Update your personal information</p>
                </div>
            </div>

            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6 space-y-5">

                <!-- Avatar -->
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div
                            class="w-16 h-16 rounded-full bg-cpa-medium flex items-center justify-center text-white text-xl font-bold overflow-hidden"
                        >
                            <img
                                v-if="user.avatar_url"
                                :src="`/storage/${user.avatar_url}`"
                                :alt="`${user.first_name} avatar`"
                                class="w-full h-full object-cover"
                            />
                            <span v-else>{{ user.first_name[0]?.toUpperCase() }}</span>
                        </div>
                        <label
                            class="absolute -bottom-1 -right-1 w-6 h-6 bg-cpa-medium-dark rounded-full flex items-center justify-center cursor-pointer hover:bg-cpa-dark transition-colors"
                            for="avatar-input"
                        >
                            <Camera :size="12" class="text-white" />
                        </label>
                        <input id="avatar-input" type="file" class="sr-only" accept="image/*" @change="onAvatarChange" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-cpa-text-primary">{{ user.first_name }} {{ user.last_name }}</p>
                        <p class="text-xs text-cpa-text-muted">{{ user.email }}</p>
                        <p class="text-xs text-cpa-text-muted mt-0.5">JPG, PNG or WebP. Max 2 MB.</p>
                    </div>
                </div>

                <hr class="border-cpa-border" />

                <!-- Name row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">First Name</label>
                        <input
                            v-model="form.first_name"
                            type="text"
                            class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium"
                            :class="{ 'border-cpa-danger': form.errors.first_name }"
                        />
                        <p v-if="form.errors.first_name" class="text-cpa-danger text-xs mt-1">{{ form.errors.first_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Last Name</label>
                        <input
                            v-model="form.last_name"
                            type="text"
                            class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium"
                            :class="{ 'border-cpa-danger': form.errors.last_name }"
                        />
                        <p v-if="form.errors.last_name" class="text-cpa-danger text-xs mt-1">{{ form.errors.last_name }}</p>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Email Address</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium"
                        :class="{ 'border-cpa-danger': form.errors.email }"
                    />
                    <p v-if="form.errors.email" class="text-cpa-danger text-xs mt-1">{{ form.errors.email }}</p>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Phone (optional)</label>
                    <input v-model="form.phone" type="tel" placeholder="+1 555 000 0000" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium placeholder:text-cpa-text-muted" />
                </div>

                <!-- Save -->
                <div class="flex justify-end pt-2">
                    <button
                        :disabled="form.processing"
                        class="flex items-center gap-1.5 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-5 py-2 text-sm transition-colors disabled:opacity-60"
                        @click="submit"
                    >
                        <Save :size="14" />
                        {{ form.processing ? 'Saving…' : 'Save Changes' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
