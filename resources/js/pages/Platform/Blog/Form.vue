<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, Save, Eye, Image, Tag } from '@lucide/vue'
import PlatformLayout from '@/layouts/PlatformLayout.vue'

interface BlogPost {
    id?: number
    title: string
    slug?: string
    excerpt?: string
    content?: string
    status: string
    category?: { id: number; name: string } | null
    tags?: Array<{ id: number; name: string; slug: string }>
    meta_title?: string
    meta_description?: string
    published_at?: string
}

const props = defineProps<{
    post: BlogPost | null
    categories: Array<{ id: number; name: string }>
}>()

const isEditing = computed(() => !!props.post?.id)

const form = useForm({
    title:           props.post?.title ?? '',
    slug:            props.post?.slug ?? '',
    excerpt:         props.post?.excerpt ?? '',
    content:         props.post?.content ?? '',
    status:          props.post?.status ?? 'draft',
    category_id:     props.post?.category?.id ?? '',
    tags:            props.post?.tags?.map(t => t.name) ?? [] as string[],
    meta_title:      props.post?.meta_title ?? '',
    meta_description:props.post?.meta_description ?? '',
    published_at:    props.post?.published_at ?? '',
})

const tagInput = ref('')

function addTag(e: KeyboardEvent) {
    const val = tagInput.value.trim()
    if ((e.key === 'Enter' || e.key === ',') && val) {
        e.preventDefault()
        if (!form.tags.includes(val)) {
            form.tags.push(val)
        }
        tagInput.value = ''
    }
}
function removeTag(tag: string) {
    form.tags = form.tags.filter(t => t !== tag)
}

function submit() {
    if (isEditing.value) {
        form.patch(`/platform/blog/${props.post!.id}`, { forceFormData: true })
    } else {
        form.post('/platform/blog', { forceFormData: true })
    }
}

const statusOptions = [
    { value: 'draft',     label: 'Draft'     },
    { value: 'published', label: 'Published' },
    { value: 'archived',  label: 'Archived'  },
]
</script>

<template>
    <PlatformLayout>
        <template #header>
            <div class="flex items-center gap-2 text-sm text-cpa-text-muted">
                <Link href="/platform/blog" class="hover:text-cpa-text-primary transition-colors">Blog</Link>
                <span>/</span>
                <span class="text-cpa-text-primary font-medium">{{ isEditing ? 'Edit Post' : 'New Post' }}</span>
            </div>
        </template>

        <div class="max-w-3xl space-y-6">

            <!-- Header -->
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <Link href="/platform/blog" class="flex items-center justify-center w-8 h-8 rounded-lg border border-cpa-border hover:bg-cpa-very-light transition-colors">
                        <ArrowLeft :size="16" class="text-cpa-text-secondary" />
                    </Link>
                    <h1 class="text-xl font-semibold text-cpa-text-primary">
                        {{ isEditing ? 'Edit Post' : 'New Blog Post' }}
                    </h1>
                </div>
                <div class="flex items-center gap-2">
                    <select
                        v-model="form.status"
                        class="px-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium"
                    >
                        <option v-for="s in statusOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
                    </select>
                    <button
                        :disabled="form.processing"
                        class="flex items-center gap-1.5 bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-4 py-2 text-sm transition-colors disabled:opacity-60"
                        @click="submit"
                    >
                        <Save :size="14" />
                        {{ form.processing ? 'Saving…' : (isEditing ? 'Save Changes' : 'Publish Post') }}
                    </button>
                </div>
            </div>

            <!-- Main content -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6 space-y-5">

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">
                        Title <span class="text-cpa-danger">*</span>
                    </label>
                    <input
                        v-model="form.title"
                        type="text"
                        placeholder="Enter post title…"
                        class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium placeholder:text-cpa-text-muted"
                        :class="{ 'border-cpa-danger': form.errors.title }"
                    />
                    <p v-if="form.errors.title" class="text-cpa-danger text-xs mt-1">{{ form.errors.title }}</p>
                </div>

                <!-- Slug -->
                <div>
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">URL Slug</label>
                    <input v-model="form.slug" type="text" placeholder="auto-generated-from-title" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg font-mono focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium placeholder:text-cpa-text-muted" />
                    <p class="text-xs text-cpa-text-muted mt-1">Leave blank to auto-generate from the title.</p>
                </div>

                <!-- Excerpt -->
                <div>
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Excerpt</label>
                    <textarea v-model="form.excerpt" rows="2" placeholder="Short summary (shown in listings and meta)…" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium placeholder:text-cpa-text-muted resize-none" />
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">
                        Content <span class="text-cpa-danger">*</span>
                    </label>
                    <textarea
                        v-model="form.content"
                        rows="16"
                        placeholder="Write your post content here (HTML or Markdown)…"
                        class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium placeholder:text-cpa-text-muted font-mono"
                        :class="{ 'border-cpa-danger': form.errors.content }"
                    />
                    <p v-if="form.errors.content" class="text-cpa-danger text-xs mt-1">{{ form.errors.content }}</p>
                </div>

                <!-- Category + Publish date row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Category</label>
                        <select v-model="form.category_id" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg bg-cpa-bg focus:outline-none focus:ring-2 focus:ring-cpa-medium">
                            <option value="">No category</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Publish Date</label>
                        <input v-model="form.published_at" type="datetime-local" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium" />
                        <p class="text-xs text-cpa-text-muted mt-1">Leave blank to publish immediately when status is Published.</p>
                    </div>
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5 flex items-center gap-1.5">
                        <Tag :size="13" /> Tags
                    </label>
                    <div class="flex flex-wrap gap-1.5 mb-2">
                        <span
                            v-for="tag in form.tags"
                            :key="tag"
                            class="inline-flex items-center gap-1 bg-cpa-very-light text-cpa-dark text-xs font-medium px-2 py-0.5 rounded-full border border-cpa-border"
                        >
                            {{ tag }}
                            <button type="button" class="text-cpa-text-muted hover:text-cpa-danger ml-0.5 leading-none" @click="removeTag(tag)">×</button>
                        </span>
                    </div>
                    <input
                        v-model="tagInput"
                        type="text"
                        placeholder="Type a tag and press Enter or comma…"
                        class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted"
                        @keydown="addTag"
                    />
                </div>
            </div>

            <!-- SEO card -->
            <div class="bg-white border border-cpa-border rounded-xl shadow-sm p-6 space-y-4">
                <h2 class="text-base font-semibold text-cpa-text-primary">SEO Settings</h2>
                <div>
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Meta Title</label>
                    <input v-model="form.meta_title" type="text" placeholder="Override the post title for search engines…" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium placeholder:text-cpa-text-muted" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-cpa-text-primary mb-1.5">Meta Description</label>
                    <textarea v-model="form.meta_description" rows="2" placeholder="160 characters max…" class="w-full px-3 py-2 text-sm border border-cpa-border rounded-lg focus:outline-none focus:ring-2 focus:ring-cpa-medium resize-none placeholder:text-cpa-text-muted" maxlength="320" />
                </div>
            </div>

        </div>
    </PlatformLayout>
</template>
