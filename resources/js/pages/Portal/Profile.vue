<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { User, Save, Camera, CheckCircle } from '@lucide/vue'
import PortalLayout from '@/layouts/PortalLayout.vue'

interface AuthUser {
    id: number
    first_name: string
    last_name: string
    email: string
    phone?: string
    avatar_url?: string
}

const props = defineProps<{ user: AuthUser }>()

const form = useForm({
    first_name: props.user.first_name ?? '',
    last_name:  props.user.last_name ?? '',
    email:      props.user.email ?? '',
    phone:      props.user.phone ?? '',
    avatar:     null as File | null,
})

function onAvatarChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (file) form.avatar = file
}

function submit() {
    form.patch(route('settings.profile.update'), { forceFormData: true })
}
</script>

<template>
    <PortalLayout>
        <template #header>
            <span class="text-sm font-medium text-cpa-text-primary">My Profile</span>
        </template>

        <div class="max-w-xl space-y-5">

            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-cpa-very-light flex items-center justify-center">
                    <User :size="18" class="text-cpa-medium-dark" />
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-cpa-text-primary">My Profile</h1>
                    <p class="text-xs text-cpa-text-muted">Update your personal information</p>
                </div>
            </div>

            <!-- Success flash -->
            <Transition name="fade">
                <div
                    v-if="$page.props.flash?.success"
                    class="flex items-center gap-2.5 bg-cpa-success-bg text-cpa-success border border-cpa-success/20 rounded-xl px-4 py-3 text-sm font-medium"
                >
                    <CheckCircle :size="16" class="flex-shrink-0" />
                    {{ $page.props.flash.success }}
                </div>
            </Transition>

            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6 space-y-5">

                <!-- Avatar -->
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-16 h-16 rounded-full bg-cpa-medium flex items-center justify-center text-white text-xl font-bold overflow-hidden select-none">
                            <img v-if="user.avatar_url" :src="`/storage/${user.avatar_url}`" :alt="`${user.first_name} avatar`" class="w-full h-full object-cover" />
                            <span v-else>{{ user.first_name[0]?.toUpperCase() }}</span>
                        </div>
                        <label class="absolute -bottom-1 -right-1 w-6 h-6 bg-cpa-medium-dark rounded-full flex items-center justify-center cursor-pointer hover:bg-cpa-dark transition-colors" for="portal-avatar">
                            <Camera :size="12" class="text-white" />
                        </label>
                        <input id="portal-avatar" type="file" class="sr-only" accept="image/jpeg,image/png,image/webp" @change="onAvatarChange" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-cpa-text-primary">{{ user.first_name }} {{ user.last_name }}</p>
                        <p class="text-xs text-cpa-text-muted">{{ user.email }}</p>
                    </div>
                </div>

                <hr class="border-cpa-border" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="p_first" class="block text-sm font-medium text-cpa-text-primary mb-1.5">First Name</label>
                        <input id="p_first" v-model="form.first_name" type="text" class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium transition-colors" :class="form.errors.first_name ? 'border-cpa-danger' : 'border-cpa-border'" />
                        <p v-if="form.errors.first_name" class="text-cpa-danger text-xs mt-1">{{ form.errors.first_name }}</p>
                    </div>
                    <div>
                        <label for="p_last" class="block text-sm font-medium text-cpa-text-primary mb-1.5">Last Name</label>
                        <input id="p_last" v-model="form.last_name" type="text" class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium transition-colors" :class="form.errors.last_name ? 'border-cpa-danger' : 'border-cpa-border'" />
                        <p v-if="form.errors.last_name" class="text-cpa-danger text-xs mt-1">{{ form.errors.last_name }}</p>
                    </div>
                </div>

                <div>
                    <label for="p_email" class="block text-sm font-medium text-cpa-text-primary mb-1.5">Email Address</label>
                    <input id="p_email" v-model="form.email" type="email" class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium transition-colors" :class="form.errors.email ? 'border-cpa-danger' : 'border-cpa-border'" />
                    <p v-if="form.errors.email" class="text-cpa-danger text-xs mt-1">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label for="p_phone" class="block text-sm font-medium text-cpa-text-primary mb-1.5">Phone <span class="text-cpa-text-muted font-normal">(optional)</span></label>
                    <input id="p_phone" v-model="form.phone" type="tel" placeholder="+1 555 000 0000" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted transition-colors" />
                </div>

                <div class="flex justify-end pt-2 border-t border-cpa-border">
                    <button :disabled="form.processing" type="button" class="flex items-center gap-1.5 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-5 py-2 text-sm transition-colors disabled:opacity-60" @click="submit">
                        <Save :size="14" />
                        {{ form.processing ? 'Saving…' : 'Save Changes' }}
                    </button>
                </div>
            </div>
        </div>
    </PortalLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
