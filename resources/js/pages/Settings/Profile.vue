<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3'
import { User, Save, Camera, CheckCircle } from '@lucide/vue'
import SettingsLayout from '@/layouts/SettingsLayout.vue'

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

// Flash message from redirect
const page = usePage()

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
    form.patch(route('settings.profile.update'), { forceFormData: true })
}
</script>

<template>
    <SettingsLayout>
        <div class="space-y-5">

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

                <div class="flex items-center gap-2 mb-1">
                    <User :size="16" class="text-cpa-medium-dark" />
                    <h2 class="text-base font-semibold text-cpa-text-primary">Profile Information</h2>
                </div>

                <!-- Avatar -->
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-16 h-16 rounded-full bg-cpa-medium flex items-center justify-center text-white text-xl font-bold overflow-hidden select-none">
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
                            aria-label="Change avatar"
                        >
                            <Camera :size="12" class="text-white" />
                        </label>
                        <input id="avatar-input" type="file" class="sr-only" accept="image/jpeg,image/png,image/webp" @change="onAvatarChange" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-cpa-text-primary">{{ user.first_name }} {{ user.last_name }}</p>
                        <p class="text-xs text-cpa-text-muted">{{ user.email }}</p>
                        <p class="text-xs text-cpa-text-muted mt-0.5">JPG, PNG or WebP — max 2 MB</p>
                    </div>
                </div>

                <hr class="border-cpa-border" />

                <!-- Name -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-cpa-text-primary mb-1.5">First Name</label>
                        <input
                            id="first_name"
                            v-model="form.first_name"
                            type="text"
                            autocomplete="given-name"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium transition-colors"
                            :class="form.errors.first_name ? 'border-cpa-danger' : 'border-cpa-border'"
                        />
                        <p v-if="form.errors.first_name" class="text-cpa-danger text-xs mt-1">{{ form.errors.first_name }}</p>
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-cpa-text-primary mb-1.5">Last Name</label>
                        <input
                            id="last_name"
                            v-model="form.last_name"
                            type="text"
                            autocomplete="family-name"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium transition-colors"
                            :class="form.errors.last_name ? 'border-cpa-danger' : 'border-cpa-border'"
                        />
                        <p v-if="form.errors.last_name" class="text-cpa-danger text-xs mt-1">{{ form.errors.last_name }}</p>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-cpa-text-primary mb-1.5">Email Address</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium transition-colors"
                        :class="form.errors.email ? 'border-cpa-danger' : 'border-cpa-border'"
                    />
                    <p v-if="form.errors.email" class="text-cpa-danger text-xs mt-1">{{ form.errors.email }}</p>
                    <p class="text-xs text-cpa-text-muted mt-1">Changing email requires re-verification.</p>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-cpa-text-primary mb-1.5">Phone <span class="text-cpa-text-muted font-normal">(optional)</span></label>
                    <input
                        id="phone"
                        v-model="form.phone"
                        type="tel"
                        autocomplete="tel"
                        placeholder="+1 555 000 0000"
                        class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium placeholder:text-cpa-text-muted transition-colors"
                    />
                </div>

                <!-- Save -->
                <div class="flex justify-end pt-2 border-t border-cpa-border">
                    <button
                        :disabled="form.processing"
                        type="button"
                        class="flex items-center gap-1.5 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-5 py-2 text-sm transition-colors disabled:opacity-60"
                        @click="submit"
                    >
                        <Save :size="14" />
                        {{ form.processing ? 'Saving…' : 'Save Changes' }}
                    </button>
                </div>
            </div>
        </div>
    </SettingsLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
