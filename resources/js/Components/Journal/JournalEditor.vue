<script setup>
import { onBeforeUnmount, ref, onMounted } from 'vue';
import axios from 'axios';
import AssetPalette from './AssetPalette.vue';
import { useToast } from '@/Composables/useToast';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    form: Object,
    formattedDate: String
});

const emit = defineEmits(['submit']);

const { success, error: toastError } = useToast();
const isFocused = ref(false);
const isRefining = ref(false);
const previewUrls = ref({});

const getPreviewUrl = (file, key) => {
    if (!file || !(file instanceof File || file instanceof Blob)) {
        return '';
    }
    
    // Revoke old URL if it exists to prevent memory leaks
    if (previewUrls.value[key]) {
        window.URL.revokeObjectURL(previewUrls.value[key]);
    }
    
    const url = window.URL.createObjectURL(file);
    previewUrls.value[key] = url;
    return url;
};

const refineContent = async () => {
    if (!props.form.content || props.form.content.length < 5 || isRefining.value || props.form.processing) return;
    
    isRefining.value = true;
    
    try {
        const response = await axios.post(route('journal.refine'), {
            content: props.form.content
        });
        
        if (response.data.refinedText) {
            props.form.content = response.data.refinedText;
            success('The Muse has refined your lore into professional archives.', 6000);
        }
    } catch (error) {
        const msg = error.response?.data?.error || 'The Muse is silent...';
        toastError(msg, 7000);
    } finally {
        isRefining.value = false;
    }
};

const isSealing = ref(false);

const handleSealDecree = () => {
    if (!props.form.content || props.form.processing || isRefining.value) return;
    
    isSealing.value = true;
    setTimeout(() => isSealing.value = false, 1000);
    
    emit('submit');
};

const wordCount = ref(0);
const updateWordCount = () => {
    const text = props.form.content || '';
    wordCount.value = text.split(/\s+/).filter(word => word.length > 0).length;
};

const handleKeydown = (e) => {
    // Ctrl+S or Cmd+S to save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        handleSealDecree();
    }
    // Ctrl+M (Muse)
    if ((e.ctrlKey || e.metaKey) && e.key === 'm') {
        e.preventDefault();
        refineContent();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
    updateWordCount();
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeydown);
    Object.values(previewUrls.value).forEach(url => window.URL.revokeObjectURL(url));
});
</script>

<template>
    <div 
        class="flex-1 flex flex-col relative overflow-hidden group/editor min-h-0"
    >
        <!-- Background (Textured but Clean) -->
        <div 
            class="absolute inset-0 bg-obsidian rounded-[2rem] border border-white/[0.04] shadow-2xl"
        ></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/dark-matter.png')] opacity-[0.03] pointer-events-none rounded-[2rem]"></div>

        <!-- Subtle Border Frame -->
        <div class="absolute inset-0 border border-white/[0.05] rounded-[2rem] pointer-events-none z-30"></div>
        
        <!-- Header -->
        <div class="px-4 md:px-10 lg:px-12 py-5 md:py-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-5 md:gap-6 relative z-20 border-b border-white/[0.04] shrink-0">
            <div class="flex-shrink-0 min-w-0 max-w-full">
                <p class="text-[8px] md:text-[9px] uppercase tracking-[0.4em] text-brass/60 font-bold mb-0.5 md:mb-1">Codex Entry</p>
                <h2 class="text-lg md:text-2xl font-bold text-cream font-cinzel italic truncate max-w-[200px] sm:max-w-[300px] md:max-w-none">{{ formattedDate }}</h2>
            </div>
            
            <div class="flex flex-row flex-wrap items-center gap-3 md:gap-4 w-full md:w-auto">
                <!-- AI Refinement Button -->
                <button 
                    class="group/muse relative h-10 md:h-11 w-full md:w-44 bg-white/[0.04] text-cream rounded-xl border border-white/[0.08] transition-all hover:bg-white/[0.1] active:scale-95 disabled:opacity-20 flex-1 md:flex-none flex items-center justify-center"
                    @click="refineContent"
                    :disabled="isRefining || form.processing || !form.content"
                >
                    <div class="flex items-center gap-2">
                        <svg 
                            :class="{'animate-spin': isRefining}"
                            class="w-3.5 h-3.5 md:w-4 md:h-4 text-brass" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="text-[10px] md:text-[11px] uppercase tracking-[0.15em] font-black whitespace-nowrap">
                            {{ isRefining ? 'Musing...' : 'Consult Muse' }}
                        </span>
                    </div>
                </button>

                <!-- Save Button -->
                <button 
                    :class="[
                        'h-10 md:h-11 w-full md:w-44 bg-brass text-void rounded-xl transition-all hover:bg-cream hover:scale-[1.02] active:scale-95 disabled:opacity-20 flex-1 md:flex-none flex items-center justify-center font-black text-[10px] md:text-[11px] uppercase tracking-[0.15em] whitespace-nowrap shadow-xl',
                        isSealing ? 'animate-seal' : ''
                    ]"
                    @click="handleSealDecree"
                    :disabled="form.processing || isRefining || !form.content"
                >
                    {{ form.processing ? 'Sealing...' : 'Seal Decree' }}
                </button>
            </div>
        </div>

        <!-- Centered Writing Area -->
        <div class="flex-1 overflow-y-auto relative z-10 scrollbar-hide">
            <div class="max-w-3xl mx-auto w-full px-6 md:px-12 pt-10 pb-20 relative">
                <!-- Title -->
                <div class="mb-10 group/title">
                    <input 
                        v-model="form.title"
                        placeholder="Chapter Title..."
                        class="w-full bg-transparent border-none focus:ring-0 p-0 text-xl md:text-3xl font-bold font-cinzel text-cream placeholder:text-brass/30 placeholder:italic"
                    />
                    <div class="h-[1px] w-12 bg-brass/20 mt-4 group-hover/title:w-20 transition-all duration-700"></div>
                    <div v-if="form.errors.title" class="text-red-400 text-xs mt-2 font-sans tracking-wide">{{ form.errors.title }}</div>
                </div>

                <!-- Content Area -->
                <textarea 
                    v-model="form.content"
                    @input="updateWordCount"
                    @focus="isFocused = true"
                    @blur="isFocused = false"
                    placeholder="Inscribe the day's lore..."
                    class="w-full bg-transparent border-none focus:ring-0 p-0 text-base md:text-lg leading-[2.2] text-cream/70 placeholder:text-cream/10 resize-none font-serif min-h-[500px]"
                    :class="{'text-cream': isFocused}"
                ></textarea>
                
                <!-- Micro-Interactions Footer -->
                <div class="mt-8 flex items-center justify-between text-brass/40 font-cinzel">
                    <div class="flex items-center gap-6">
                        <div class="flex flex-col">
                            <span class="text-[8px] uppercase tracking-widest leading-none mb-1">Word Count</span>
                            <span class="text-xs font-bold text-brass/60">{{ wordCount }} Words</span>
                        </div>
                        <div class="h-6 w-[1px] bg-white/[0.05]"></div>
                        <div class="group relative cursor-help">
                            <span class="text-[8px] uppercase tracking-widest">Keyboard Glyphs</span>
                            <div class="absolute bottom-full left-0 mb-2 p-2 bg-void border border-white/10 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-2xl">
                                <p class="text-[10px] text-brass/70 mb-1 leading-relaxed"><span class="text-brass font-bold">Ctrl + S:</span> Seal Decree</p>
                                <p class="text-[10px] text-brass/70 leading-relaxed"><span class="text-brass font-bold">Ctrl + M:</span> Consult Muse</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="form.errors.content" class="text-red-400 text-xs mt-2 font-sans tracking-wide">{{ form.errors.content }}</div>

                <!-- Attachments (Subtle Footer Style) -->
                <div v-if="form.image || form.video || form.audio || form.file" class="mt-16 pt-10 border-t border-white/[0.05] space-y-6">
                    <p class="text-[9px] uppercase tracking-[0.4em] text-brass/50 font-bold">Chronicle Assets</p>
                    <div class="flex flex-wrap gap-5">
                        <div 
                            v-for="type in ['image', 'video', 'audio', 'file'].filter(t => form[t])" 
                            :key="type"
                            class="group/asset-item relative h-20 w-32 bg-white/[0.03] border border-white/[0.05] rounded-xl flex flex-col items-center justify-center transition-all hover:border-brass/30 overflow-hidden"
                        >
                            <img v-if="type === 'image'" :src="getPreviewUrl(form.image, 'image')" class="absolute inset-0 w-full h-full object-cover opacity-40 group-hover/asset-item:opacity-60 transition-opacity" />
                            <div class="relative z-10 flex flex-col items-center">
                                <svg class="w-4 h-4 text-brass mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                <span class="text-[8px] text-cream uppercase tracking-widest truncate w-24 text-center font-bold">{{ form[type].name || type }}</span>
                            </div>
                            <button @click="form[type] = null" class="absolute inset-0 bg-black/90 opacity-0 group-hover/asset-item:opacity-100 flex items-center justify-center transition-opacity">
                                <span class="text-[8px] text-white uppercase font-black tracking-widest">Discard Entry</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Minimal Asset Peeking Palette (Right Side) -->
        <div class="absolute right-0 top-1/2 -translate-y-1/2 z-40 group-hover/editor:translate-x-[-10px] transition-all duration-700">
            <AssetPalette :form="form" />
        </div>
    </div>
</template>

<style scoped>
@keyframes shimmer {
    100% { transform: translateX(100%); }
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.cubic-bezier-standard {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

